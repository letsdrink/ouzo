<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;
use InvalidArgumentException;
use Ouzo\Db;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Query;
use Ouzo\Db\QueryExecutor;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationFetcher;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use PDO;
use ReflectionClass;

class Model extends Validatable
{
    /**
     * @var Db
     */
    private $_db;
    private $_attributes;
    private $_tableName;
    private $_sequenceName;
    private $_primaryKeyName;
    private $_fields;
    private $_relations;
    private $_afterSaveCallbacks = array();
    private $_beforeSaveCallbacks = array();
    private $_updatedAttributes = array();

    /**
     * Creates a new model object.
     * Accepted parameters:
     * <code>
     * 'table' - defaults to pluralized class name. E.g. customer_orders for CustomerOrder
     * 'primaryKey' - defaults to 'id'
     * 'sequence' - defaults to 'table_primaryKey_seq'
     *
     * 'hasOne' => array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * 'hasMany' => array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))
     * 'belongsTo' => array('name' => array('class' => 'Class'))
     *
     * 'beforeSave' => array('_beforeSave', function ($attributes) { ... })
     * 'afterSave' => array('_afterSave', function ($attributes) { ... })
     *
     * 'fields' - mapped column names
     * 'attributes' -  array of column => value
     * </code>
     */
    public function __construct(array $params)
    {
        $this->_prepareParameters($params);

        $tableName = Arrays::getValue($params, 'table') ?: Strings::tableize($this->getModelName());
        $primaryKeyName = Arrays::getValue($params, 'primaryKey', 'id');
        $sequenceName = Arrays::getValue($params, 'sequence', "{$tableName}_{$primaryKeyName}_seq");

        list($attributes, $fields) = $this->_extractFieldsAndAttributes($params);

        $this->_relations = RelationsCache::getRelations(get_called_class(), $params, $primaryKeyName);

        if (isset($attributes[$primaryKeyName]) && Strings::isBlank($attributes[$primaryKeyName])) {
            unset($attributes[$primaryKeyName]);
        }

        $this->_tableName = $tableName;
        $this->_sequenceName = $sequenceName;
        $this->_primaryKeyName = $primaryKeyName;
        $this->_db = empty($params['db']) ? Db::getInstance() : $params['db'];
        $this->_fields = $fields;
        if ($primaryKeyName && !in_array($primaryKeyName, $this->_fields)) {
            $this->_fields[] = $primaryKeyName;
        }
        $this->_attributes = $this->filterAttributes($attributes);
        $this->_updatedAttributes = array_keys($this->_attributes);
        $this->_afterSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'afterSave'));
        $this->_beforeSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'beforeSave'));
    }

    public function __set($name, $value)
    {
        $this->_updatedAttributes[] = $name;
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
        if ($this->_relations->hasRelation($name)) {
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
        $this->_updatedAttributes = array_merge($this->_updatedAttributes, array_keys($attributes));
        $this->_attributes = array_merge($this->_attributes, $this->filterAttributesPreserveNull($attributes));
    }

    private function filterAttributesPreserveNull($data)
    {
        return array_intersect_key($data, array_flip($this->_fields));
    }

    private function filterAttributes($data)
    {
        return array_filter($this->filterAttributesPreserveNull($data), function ($var) {
            return !is_null($var);
        });
    }

    public function attributes()
    {
        return array_replace(array_map(function () {
            return null;
        }, array_flip($this->_fields)), $this->_attributes);
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
        return $this->_tableName;
    }

    public function insert()
    {
        $this->_callBeforeSaveCallbacks();

        $primaryKey = $this->_primaryKeyName;
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);

        $query = Query::insert($attributes)->into($this->_tableName);
        $lastInsertedId = QueryExecutor::prepare($this->_db, $query)->insert($this->_sequenceName);

        if ($primaryKey && $this->_sequenceName) {
            $this->$primaryKey = $lastInsertedId;
        }

        $this->_callAfterSaveCallbacks();

        $this->_resetUpdates();

        return $lastInsertedId;
    }

    public function update()
    {
        $this->_callBeforeSaveCallbacks();

        $attributes = $this->getAttributesForUpdate();
        if ($attributes) {
            $query = Query::update($attributes)
                ->table($this->_tableName)
                ->where(array($this->_primaryKeyName => $this->getId()));

            QueryExecutor::prepare($this->_db, $query)->execute();
        }

        $this->_callAfterSaveCallbacks();
    }

    function _callAfterSaveCallbacks()
    {
        $this->_callCallbacks($this->_afterSaveCallbacks);
    }

    function _callBeforeSaveCallbacks()
    {
        $this->_callCallbacks($this->_beforeSaveCallbacks);
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
        $this->_updatedAttributes = array_merge($this->_updatedAttributes, array_keys($attributes));
        $this->assignAttributes($attributes);
        if ($this->isValid()) {
            $this->update();
            return true;
        }
        return false;
    }

    public function delete()
    {
        return (bool)$this->where(array($this->_primaryKeyName => $this->getId()))->deleteAll();
    }

    public function getId()
    {
        $primaryKeyName = $this->_primaryKeyName;
        return $this->$primaryKeyName;
    }

    public function getIdName()
    {
        return $this->_primaryKeyName;
    }

    public function getSequenceName()
    {
        return $this->_sequenceName;
    }

    private function _findByIdOrNull($value)
    {
        return $this->where(array($this->_primaryKeyName => $value))->fetch();
    }

    private function _findById($value)
    {
        if (!$this->_primaryKeyName) {
            throw new DbException('Primary key is not defined for table ' . $this->_tableName);
        }
        $result = $this->_findByIdOrNull($value);
        if (!$result) {
            throw new DbException($this->_tableName . " with " . $this->_primaryKeyName . "=" . $value . " not found");
        }
        return $result;
    }

    /**
     * Returns model object as a nicely formatted string.
     */
    public function inspect()
    {
        return get_called_class() . "\n" . print_r($this->attributes(), true);
    }

    public function getModelName()
    {
        $function = new ReflectionClass($this);
        return $function->getShortName();
    }

    public function _getFields()
    {
        return $this->_fields;
    }

    public static function getFields()
    {
        return static::metaInstance()->_getFields();
    }

    private function _getFieldsWithoutPrimaryKey()
    {
        return array_diff($this->_fields, array($this->_primaryKeyName));
    }

    public static function getFieldsWithoutPrimaryKey()
    {
        return static::metaInstance()->_getFieldsWithoutPrimaryKey();
    }

    private function _fetchRelation($name)
    {
        $relation = $this->getRelation($name);
        $relationFetcher = new RelationFetcher($relation);
        $results = array($this);
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
        $object->_resetUpdates();
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
        return new ModelQueryBuilder($obj, $obj->_db, $alias);
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
    public static function find($where, $whereValues, $orderBy = array(), $limit = 0, $offset = 0)
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
        $results = $meta->_db->query($nativeSql, Arrays::toArray($params))->fetchAll();

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
        if (!$instance->isValid()) {
            throw new ValidationException("Validation has failed for object: " . $instance->inspect(), $instance->getErrors());
        }
        $instance->insert();
        return $instance;
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
        return $this->_relations->getRelation($name);
    }

    public function __toString()
    {
        return $this->inspect();
    }

    private function _extractFieldsAndDefaults($fields)
    {
        $newFields = array();
        $defaults = array();
        $fieldKeys = array_keys($fields);
        foreach ($fieldKeys as $fieldKey) {
            if (is_numeric($fieldKey)) {
                $newFields[] = $fields[$fieldKey];
            } else {
                $newFields[] = $fieldKey;
                $value = $fields[$fieldKey];
                if (is_callable($value)) {
                    $value = $value();
                }
                $defaults[$fieldKey] = $value;
            }
        }
        return array($newFields, $defaults);
    }

    private function _extractFieldsAndAttributes(array $params)
    {
        $attributes = $params['attributes'];
        $fields = $params['fields'];

        list($fields, $defaults) = $this->_extractFieldsAndDefaults($fields);
        $attributes = array_merge($defaults, $attributes);
        return array($attributes, $fields);
    }

    function _resetUpdates()
    {
        $this->_updatedAttributes = array();
    }

    private function getAttributesForUpdate()
    {
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);
        return array_intersect_key($attributes, array_flip($this->_updatedAttributes));
    }
}
