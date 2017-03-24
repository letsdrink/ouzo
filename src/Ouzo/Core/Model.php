<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Exception;
use InvalidArgumentException;
use Ouzo\Api\ValidationException;
use Ouzo\Db\BatchLoadingSession;
use Ouzo\Db\ModelDefinition;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Query;
use Ouzo\Db\QueryExecutor;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationFetcher;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use PDO;
use ReflectionClass;

class Model extends Validatable
{
    /** @var ModelDefinition */
    private $_modelDefinition;

    /** @var array */
    private $_attributes;

    /** @var array */
    private $_modifiedFields = array();

    /**
     * Creates a new model object.
     * Accepted parameters:
     * @param array $params {
     * @var string $table defaults to pluralized class name. E.g. customer_orders for CustomerOrder
     * @var string $primaryKey defaults to id
     * @var string $sequence defaults to table_primaryKey_seq
     * @var string $hasMany specification of a has-many relation e.g. array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * @var string $hasOne specification of a has-one relation e.g. array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * @var string $belongsTo specification of a belongs-to relation e.g. array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * @var string $fields mapped column names
     * @var string $attributes array of column => value
     * @var string $beforeSave function to invoke before insert or update
     * @var string $afterSave function to invoke after insert or update
     * }
     */
    public function __construct(array $params)
    {
        $this->_prepareParameters($params);

        $this->_modelDefinition = ModelDefinition::get(get_called_class(), $params);
        $primaryKeyName = $this->_modelDefinition->primaryKey;
        $attributes = $this->_modelDefinition->mergeWithDefaults($params['attributes'], $params['fields']);

        if (isset($attributes[$primaryKeyName]) && Strings::isBlank($attributes[$primaryKeyName])) {
            unset($attributes[$primaryKeyName]);
        }
        $this->_attributes = $this->filterAttributes($attributes);
        $this->_modifiedFields = array_keys($this->_attributes);
    }

    public function __set($name, $value)
    {
        $this->_modifiedFields[] = $name;
        $this->_attributes[$name] = $value;
    }

    public function __get($name)
    {
        if (empty($name)) {
            throw new Exception('Illegal attribute: field name for Model cannot be empty');
        }
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        }

        if ($this->_modelDefinition->relations->hasRelation($name)) {
            $this->_fetchRelation($name);
            return $this->_attributes[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return $this->__get($name) !== null;
    }

    public function __unset($name)
    {
        unset($this->_attributes[$name]);
    }

    public function assignAttributes($attributes)
    {
        $this->_modifiedFields = array_merge($this->_modifiedFields, array_keys($attributes));
        $this->_attributes = array_merge($this->_attributes, $this->filterAttributesPreserveNull($attributes));
    }

    private function filterAttributesPreserveNull($data)
    {
        return array_intersect_key($data, array_flip($this->_modelDefinition->fields));
    }

    private function filterAttributes($data)
    {
        return Arrays::filter($this->filterAttributesPreserveNull($data), Functions::notNull());
    }

    public function attributes()
    {
        return array_replace(array_fill_keys($this->_modelDefinition->fields, null), $this->_attributes);
    }

    public function definedAttributes()
    {
        return $this->filterAttributesPreserveNull($this->attributes());
    }

    private function _prepareParameters(array &$params)
    {
        if (empty($params['attributes'])) {
            $params['attributes'] = array();
        }
        if (empty($params['fields'])) {
            throw new InvalidArgumentException("Fields are required");
        }
    }

    public function getTableName()
    {
        return $this->_modelDefinition->table;
    }

    public function insert()
    {
        $this->_callBeforeSaveCallbacks();

        $primaryKey = $this->_modelDefinition->primaryKey;
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);

        $query = Query::insert($attributes)->into($this->_modelDefinition->table);
        $lastInsertedId = QueryExecutor::prepare($this->_modelDefinition->db, $query)->insert($this->_modelDefinition->sequence);

        if ($primaryKey && $this->_modelDefinition->sequence) {
            $this->$primaryKey = $lastInsertedId;
        }

        $this->_callAfterSaveCallbacks();

        $this->_resetModifiedFields();

        return $lastInsertedId;
    }

    public function update()
    {
        $this->_callBeforeSaveCallbacks();

        $attributes = $this->getAttributesForUpdate();
        if ($attributes) {
            $query = Query::update($attributes)
                ->table($this->_modelDefinition->table)
                ->where(array($this->_modelDefinition->primaryKey => $this->getId()));

            QueryExecutor::prepare($this->_modelDefinition->db, $query)->execute();
        }

        $this->_callAfterSaveCallbacks();
    }

    public function _callAfterSaveCallbacks()
    {
        $this->_callCallbacks($this->_modelDefinition->afterSaveCallbacks);
    }

    public function _callBeforeSaveCallbacks()
    {
        $this->_callCallbacks($this->_modelDefinition->beforeSaveCallbacks);
    }

    private function _callCallbacks($callbacks)
    {
        foreach ($callbacks as $callback) {
            if (is_string($callback)) {
                $callback = array($this, $callback);
            }
            call_user_func($callback, $this);
        }
    }

    public function insertOrUpdate()
    {
        $this->isNew() ? $this->insert() : $this->update();
    }

    public function isNew()
    {
        return !$this->getId();
    }

    public function updateAttributes($attributes)
    {
        if (!$this->updateAttributesIfValid($attributes)) {
            throw new ValidationException($this->getErrorObjects());
        }
    }

    public function updateAttributesIfValid($attributes)
    {
        $this->_modifiedFields = array_merge($this->_modifiedFields, array_keys($attributes));
        $this->assignAttributes($attributes);
        if ($this->isValid()) {
            $this->update();
            return true;
        }
        return false;
    }

    public function delete()
    {
        return (bool)$this->where(array($this->_modelDefinition->primaryKey => $this->getId()))->deleteAll();
    }

    public function getId()
    {
        $primaryKeyName = $this->_modelDefinition->primaryKey;
        return $this->$primaryKeyName;
    }

    public function getIdName()
    {
        return $this->_modelDefinition->primaryKey;
    }

    public function getSequenceName()
    {
        return $this->_modelDefinition->sequence;
    }

    private function _findByIdOrNull($value)
    {
        return $this->where(array($this->_modelDefinition->primaryKey => $value))->fetch();
    }

    private function _findById($value)
    {
        if (!$this->_modelDefinition->primaryKey) {
            throw new DbException('Primary key is not defined for table ' . $this->_modelDefinition->table);
        }
        $result = $this->_findByIdOrNull($value);
        if ($result) {
            return $result;
        }
        throw new DbException($this->_modelDefinition->table . " with " . $this->_modelDefinition->primaryKey . "=" . $value . " not found");
    }

    /**
     * Returns model object as a nicely formatted string.
     */
    public function inspect()
    {
        return get_called_class() . Objects::toString(Arrays::filter($this->_attributes, Functions::notNull()));
    }

    public function getModelName()
    {
        $function = new ReflectionClass($this);
        return $function->getShortName();
    }

    public function _getFields()
    {
        return $this->_modelDefinition->fields;
    }

    public static function getFields()
    {
        return static::metaInstance()->_getFields();
    }

    private function _getFieldsWithoutPrimaryKey()
    {
        return array_diff($this->_modelDefinition->fields, array($this->_modelDefinition->primaryKey));
    }

    public static function getFieldsWithoutPrimaryKey()
    {
        return static::metaInstance()->_getFieldsWithoutPrimaryKey();
    }

    private function _fetchRelation($name)
    {
        $relation = $this->getRelation($name);
        $relationFetcher = new RelationFetcher($relation);
        $results = BatchLoadingSession::getBatch($this);
        $relationFetcher->transform($results);
    }

    /**
     * @param array $attributes
     * @return static
     */
    public static function newInstance(array $attributes)
    {
        $className = get_called_class();
        $object = new $className($attributes);
        $object->_resetModifiedFields();
        return $object;
    }

    /**
     * @return static
     */
    public static function metaInstance()
    {
        return MetaModelCache::getMetaInstance(get_called_class());
    }

    /**
     * @return Model[]
     */
    public static function all()
    {
        return static::queryBuilder()->fetchAll();
    }

    /**
     * @param $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public static function select($columns, $type = PDO::FETCH_NUM)
    {
        return static::queryBuilder()->select($columns, $type);
    }

    /**
     * @param $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public static function selectDistinct($columns, $type = PDO::FETCH_NUM)
    {
        return static::queryBuilder()->selectDistinct($columns, $type);
    }

    /**
     * @param $relation
     * @param null $alias
     * @param string $type
     * @param array $on
     * @return ModelQueryBuilder
     */
    public static function join($relation, $alias = null, $type = 'LEFT', $on = array())
    {
        return static::queryBuilder()->join($relation, $alias, $type, $on);
    }

    /**
     * @param $relation
     * @param null $alias
     * @param array $on
     * @return ModelQueryBuilder
     */
    public static function innerJoin($relation, $alias = null, $on = array())
    {
        return static::queryBuilder()->innerJoin($relation, $alias, $on);
    }

    /**
     * @param $relation
     * @param null $alias
     * @param array $on
     * @return ModelQueryBuilder
     */
    public static function rightJoin($relation, $alias = null, $on = array())
    {
        return static::queryBuilder()->rightJoin($relation, $alias, $on);
    }

    /**
     * @param $relation
     * @param null $alias
     * @return ModelQueryBuilder
     */
    public static function using($relation, $alias = null)
    {
        return static::queryBuilder()->using($relation, $alias);
    }


    /**
     * @param string $params
     * @param array $values
     * @return ModelQueryBuilder
     */
    public static function where($params = '', $values = array())
    {
        return static::queryBuilder()->where($params, $values);
    }

    /**
     * @param null $alias
     * @return ModelQueryBuilder
     */
    public static function queryBuilder($alias = null)
    {
        $obj = static::metaInstance();
        return new ModelQueryBuilder($obj, $obj->_modelDefinition->db, $alias);
    }

    public static function count($where = '', $bindValues = null)
    {
        return static::metaInstance()->where($where, $bindValues)->count();
    }

    public static function alias($alias)
    {
        return static::queryBuilder($alias);
    }

    /**
     * @param $where
     * @param $whereValues
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return Model[]
     */
    public static function find($where, $whereValues, $orderBy = array(), $limit = null, $offset = null)
    {
        return static::metaInstance()->where($where, $whereValues)->order($orderBy)->limit($limit)->offset($offset)->fetchAll();
    }

    /** Executes a native sql and returns an array of model objects created by passing every result row to the model constructor.
     * @param $nativeSql - database specific sql
     * @param array $params - bind parameters
     * @return Model[]
     */
    public static function findBySql($nativeSql, $params = array())
    {
        $meta = static::metaInstance();
        $results = $meta->_modelDefinition->db->query($nativeSql, Arrays::toArray($params))->fetchAll();

        return Arrays::map($results, function ($row) use ($meta) {
            return $meta->newInstance($row);
        });
    }

    /**
     * @param $value
     * @return static
     */
    public static function findById($value)
    {
        return static::metaInstance()->_findById($value);
    }

    /**
     * @param $value
     * @return static
     */
    public static function findByIdOrNull($value)
    {
        return static::metaInstance()->_findByIdOrNull($value);
    }

    /**
     * @param $attributes
     * @throws ValidationException
     * @return static
     */
    public static function create(array $attributes = array())
    {
        $instance = static::newInstance($attributes);
        if ($instance->isValid()) {
            $instance->insert();
            return $instance;
        }
        throw new ValidationException("Validation has failed for object: " . $instance->inspect(), $instance->getErrors());
    }

    /**
     * Should be used for tests purposes only.
     *
     * @param $attributes
     * @return static
     */
    public static function createWithoutValidation(array $attributes = array())
    {
        $instance = static::newInstance($attributes);
        $instance->insert();
        return $instance;
    }

    /**
     * @return static
     */
    public function reload()
    {
        $this->_attributes = $this->findById($this->getId())->_attributes;
        return $this;
    }

    public function nullifyIfEmpty()
    {
        $fields = func_get_args();
        foreach ($fields as $field) {
            if (isset($this->$field) && !is_bool($this->$field) && Strings::isBlank($this->$field)) {
                $this->$field = null;
            }
        }
    }

    public function get($names, $default = null)
    {
        return Objects::getValueRecursively($this, $names, $default);
    }

    /**
     * @param $name
     * @return Relation
     */
    public function getRelation($name)
    {
        return $this->_modelDefinition->relations->getRelation($name);
    }

    public function __toString()
    {
        return $this->inspect();
    }

    public function _resetModifiedFields()
    {
        $this->_modifiedFields = array();
    }

    private function getAttributesForUpdate()
    {
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);
        return array_intersect_key($attributes, array_flip($this->_modifiedFields));
    }
}
