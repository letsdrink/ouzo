<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Exception;
use InvalidArgumentException;
use JsonSerializable;
use Ouzo\Db\BatchLoadingSession;
use Ouzo\Db\ModelDefinition;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Query;
use Ouzo\Db\QueryExecutor;
use Ouzo\Db\Relation;
use Ouzo\Db\RelationFetcher;
use Ouzo\Exception\ValidationException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use PDO;
use ReflectionClass;
use Serializable;

class Model extends Validatable implements Serializable, JsonSerializable
{
    /** @var ModelDefinition */
    private $modelDefinition;
    /** @var array */
    private $attributes;
    /** @var array */
    private $modifiedFields = [];

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
        $this->prepareParameters($params);

        $this->modelDefinition = ModelDefinition::get(get_called_class(), $params);
        $primaryKeyName = $this->modelDefinition->primaryKey;
        $attributes = $this->modelDefinition->mergeWithDefaults($params['attributes'], $params['fields']);

        if (isset($attributes[$primaryKeyName]) && Strings::isBlank($attributes[$primaryKeyName])) {
            unset($attributes[$primaryKeyName]);
        }
        $this->attributes = $this->filterAttributes($attributes);
        $this->modifiedFields = array_keys($this->attributes);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->modifiedFields[] = $name;
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (empty($name)) {
            throw new Exception('Illegal attribute: field name for Model cannot be empty');
        }
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if ($this->hasRelation($name)) {
            $this->fetchRelation($name);
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->__get($name) !== null;
    }

    /**
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * @param array $attributes
     */
    public function assignAttributes(array $attributes)
    {
        unset($attributes[$this->modelDefinition->primaryKey]);
        $this->modifiedFields = array_merge($this->modifiedFields, array_keys($attributes));
        $this->attributes = array_merge($this->attributes, $this->filterAttributesPreserveNull($attributes));
    }

    /**
     * @param array $data
     * @return array
     */
    private function filterAttributesPreserveNull(array $data)
    {
        return array_intersect_key($data, array_flip($this->modelDefinition->fields));
    }

    /**
     * @param array $data
     * @return array
     */
    private function filterAttributes(array $data)
    {
        return Arrays::filter($this->filterAttributesPreserveNull($data), Functions::notNull());
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_replace(array_fill_keys($this->modelDefinition->fields, null), $this->attributes);
    }

    /**
     * @return array
     */
    public function definedAttributes()
    {
        return $this->filterAttributesPreserveNull($this->attributes());
    }

    /**
     * @param array $params
     * @return void
     */
    private function prepareParameters(array &$params)
    {
        if (empty($params['attributes'])) {
            $params['attributes'] = [];
        }
        if (empty($params['fields'])) {
            throw new InvalidArgumentException("Fields are required");
        }
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->modelDefinition->table;
    }

    /**
     * @return int|null
     */
    public function insert()
    {
        return $this->doInsert(function ($attributes) {
            return Query::insert($attributes)->into($this->modelDefinition->table);
        });
    }

    /**
     * @return int|null
     */
    public function insertOrDoNothing()
    {
        return $this->doInsert(function ($attributes) {
            return Query::insertOrDoNoting($attributes)->into($this->modelDefinition->table);
        });
    }

    /**
     * @param array $upsertConflictColumns
     * @return int|null
     */
    public function upsert(array $upsertConflictColumns = [])
    {
        return $this->doInsert(function ($attributes) use ($upsertConflictColumns) {
            if (empty($upsertConflictColumns)) {
                $upsertConflictColumns = [$this->getIdName()];
            }
            return Query::upsert($attributes)->onConflict($upsertConflictColumns)->table($this->modelDefinition->table);
        });
    }

    /**
     * @return int|null
     */
    private function doInsert($callback)
    {
        $this->callBeforeSaveCallbacks();

        $primaryKey = $this->modelDefinition->primaryKey;
        $attributes = $this->filterAttributesPreserveNull($this->attributes);

        $query = $callback($attributes);

        $sequence = $primaryKey && $this->$primaryKey !== null ? null : $this->modelDefinition->sequence;
        $lastInsertedId = QueryExecutor::prepare($this->modelDefinition->db, $query)->insert($sequence);

        if ($primaryKey) {
            if ($sequence) {
                $this->$primaryKey = $lastInsertedId;
            } else {
                $lastInsertedId = $this->$primaryKey;
            }
        }

        $this->callAfterSaveCallbacks();
        $this->resetModifiedFields();

        return $lastInsertedId;
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->callBeforeSaveCallbacks();

        $attributes = $this->getAttributesForUpdate();
        if ($attributes) {
            $query = Query::update($attributes)
                ->table($this->modelDefinition->table)
                ->where([$this->modelDefinition->primaryKey => $this->getId()]);

            QueryExecutor::prepare($this->modelDefinition->db, $query)->execute();
        }

        $this->callAfterSaveCallbacks();
        $this->resetModifiedFields();
    }

    /**
     * @return void
     */
    public function callAfterSaveCallbacks()
    {
        $this->callCallbacks($this->modelDefinition->afterSaveCallbacks);
    }

    /**
     * @return void
     */
    public function callBeforeSaveCallbacks()
    {
        $this->callCallbacks($this->modelDefinition->beforeSaveCallbacks);
    }

    /**
     * @param array $callbacks
     * @return void
     */
    private function callCallbacks($callbacks)
    {
        foreach ($callbacks as $callback) {
            if (is_string($callback)) {
                $callback = [$this, $callback];
            }
            call_user_func($callback, $this);
        }
    }

    /**
     * @return void
     */
    public function insertOrUpdate()
    {
        $this->isNew() ? $this->insert() : $this->update();
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return !$this->getId();
    }

    /**
     * @param array $attributes
     * @throws ValidationException
     */
    public function updateAttributes(array $attributes)
    {
        if (!$this->updateAttributesIfValid($attributes)) {
            throw new ValidationException($this->getErrorObjects());
        }
    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function updateAttributesIfValid(array $attributes)
    {
        $this->assignAttributes($attributes);
        if ($this->isValid()) {
            $this->update();
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return (bool)$this->where([$this->modelDefinition->primaryKey => $this->getId()])->deleteAll();
    }

    /**
     * @return int
     */
    public function getId()
    {
        $primaryKeyName = $this->modelDefinition->primaryKey;
        return $this->$primaryKeyName;
    }

    /**
     * @return string
     */
    public function getIdName()
    {
        return $this->modelDefinition->primaryKey;
    }

    /**
     * @return string
     */
    public function getSequenceName()
    {
        return $this->modelDefinition->sequence;
    }

    /**
     * Returns model object as a nicely formatted string.
     * @return string
     */
    public function inspect()
    {
        return get_called_class() . Objects::toString(Arrays::filter($this->attributes, Functions::notNull()));
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        $function = new ReflectionClass($this);
        return $function->getShortName();
    }

    /**
     * @return array
     */
    public static function getFields()
    {
        return static::metaInstance()->_getFields();
    }

    /**
     * @return array
     */
    public function _getFields()
    {
        return $this->modelDefinition->fields;
    }

    /**
     * @return array
     */
    public static function getFieldsWithoutPrimaryKey()
    {
        return static::metaInstance()->_getFieldsWithoutPrimaryKey();
    }

    /**
     * @return array
     */
    private function _getFieldsWithoutPrimaryKey()
    {
        return array_diff($this->modelDefinition->fields, [$this->modelDefinition->primaryKey]);
    }

    /**
     * @param string $name
     * @return void
     */
    private function fetchRelation($name)
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
        /** @var Model $object */
        $object = new $className($attributes);
        $object->resetModifiedFields();
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
     * @param array|string $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public static function select($columns, $type = PDO::FETCH_NUM)
    {
        return static::queryBuilder()->select($columns, $type);
    }

    /**
     * @param array|string $columns
     * @param int $type
     * @return ModelQueryBuilder
     */
    public static function selectDistinct($columns, $type = PDO::FETCH_NUM)
    {
        return static::queryBuilder()->selectDistinct($columns, $type);
    }

    /**
     * @param string $relation
     * @param null|string $alias
     * @param string $type
     * @param array $on
     * @return ModelQueryBuilder
     */
    public static function join($relation, $alias = null, $type = 'LEFT', $on = [])
    {
        return static::queryBuilder()->join($relation, $alias, $type, $on);
    }

    /**
     * @param string $relation
     * @param null|string $alias
     * @param array $on
     * @return ModelQueryBuilder
     */
    public static function innerJoin($relation, $alias = null, $on = [])
    {
        return static::queryBuilder()->innerJoin($relation, $alias, $on);
    }

    /**
     * @param string $relation
     * @param null|string $alias
     * @param array $on
     * @return ModelQueryBuilder
     */
    public static function rightJoin($relation, $alias = null, $on = [])
    {
        return static::queryBuilder()->rightJoin($relation, $alias, $on);
    }

    /**
     * @param string $relation
     * @param null|string $alias
     * @return ModelQueryBuilder
     */
    public static function using($relation, $alias = null)
    {
        return static::queryBuilder()->using($relation, $alias);
    }

    /**
     * @param string|array $params
     * @param array $values
     * @return ModelQueryBuilder
     */
    public static function where($params = '', $values = [])
    {
        return static::queryBuilder()->where($params, $values);
    }

    /**
     * @param null|string $alias
     * @return ModelQueryBuilder
     */
    public static function queryBuilder($alias = null)
    {
        $obj = static::metaInstance();
        return new ModelQueryBuilder($obj, $obj->modelDefinition->db, $alias);
    }

    /**
     * @param string|array $where
     * @param null|array $bindValues
     * @return int
     */
    public static function count($where = '', $bindValues = null)
    {
        return static::metaInstance()->where($where, $bindValues)->count();
    }

    /**
     * @param string $alias
     * @return ModelQueryBuilder
     */
    public static function alias($alias)
    {
        return static::queryBuilder($alias);
    }

    /**
     * @param array|string $where
     * @param array $whereValues
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return Model[]
     */
    public static function find($where, $whereValues, $orderBy = [], $limit = null, $offset = null)
    {
        return static::metaInstance()->where($where, $whereValues)->order($orderBy)->limit($limit)->offset($offset)->fetchAll();
    }

    /** Executes a native sql and returns an array of model objects created by passing every result row to the model constructor.
     * @param string $nativeSql - database specific sql
     * @param array $params - bind parameters
     * @return Model[]
     */
    public static function findBySql($nativeSql, $params = [])
    {
        $meta = static::metaInstance();
        $results = $meta->modelDefinition->db->query($nativeSql, Arrays::toArray($params))->fetchAll();

        return Arrays::map($results, function ($row) use ($meta) {
            return $meta->newInstance($row);
        });
    }

    /**
     * @param int $value
     * @return static
     */
    public static function findById($value)
    {
        return static::metaInstance()->_findById($value);
    }

    /**
     * @param int $value
     * @return Model
     * @throws DbException
     */
    private function _findById($value)
    {
        if (!$this->modelDefinition->primaryKey) {
            throw new DbException('Primary key is not defined for table ' . $this->modelDefinition->table);
        }
        $result = $this->_findByIdOrNull($value);
        if ($result) {
            return $result;
        }
        throw new DbException($this->modelDefinition->table . " with " . $this->modelDefinition->primaryKey . "=" . $value . " not found");
    }

    /**
     * @param int $value
     * @return static
     */
    public static function findByIdOrNull($value)
    {
        return static::metaInstance()->_findByIdOrNull($value);
    }

    /**
     * @param int $value
     * @return Model
     */
    private function _findByIdOrNull($value)
    {
        return $this->where([$this->modelDefinition->primaryKey => $value])->fetch();
    }

    /**
     * @param $attributes
     * @throws ValidationException
     * @return static
     */
    public static function create(array $attributes = [])
    {
        $instance = static::newInstance($attributes);
        if ($instance->isValid()) {
            $instance->insert();
            return $instance;
        }
        throw new ValidationException($instance->getErrorObjects());
    }

    /**
     * @param $attributes
     * @param $upsertConflictColumns
     * @throws ValidationException
     * @return static
     */
    public static function createOrUpdate(array $attributes = [], array $upsertConflictColumns = [])
    {
        $instance = static::newInstance($attributes);
        if ($instance->isValid()) {
            $instance->upsert($upsertConflictColumns);
            return $instance;
        }
        throw new ValidationException($instance->getErrorObjects());
    }

    /**
     * Should be used for tests purposes only.
     *
     * @param array $attributes
     * @return static
     */
    public static function createWithoutValidation(array $attributes = [])
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
        $this->attributes = $this->findById($this->getId())->attributes;
        $this->resetModifiedFields();

        return $this;
    }

    /**
     * @param array $fields
     * @return void
     */
    public function nullifyIfEmpty(...$fields)
    {
        foreach ($fields as $field) {
            if (isset($this->$field) && !is_bool($this->$field) && Strings::isBlank($this->$field)) {
                $this->$field = null;
            }
        }
    }

    /**
     * @param string $names
     * @param null|mixed $default
     * @return mixed|null
     */
    public function get($names, $default = null)
    {
        return Objects::getValueRecursively($this, $names, $default);
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasRelation($name)
    {
        return $this->modelDefinition->relations->hasRelation($name);
    }

    /**
     * @param string $name
     * @return Relation
     */
    public function getRelation($name)
    {
        return $this->modelDefinition->relations->getRelation($name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->inspect();
    }

    /**
     * @return void
     */
    public function resetModifiedFields()
    {
        $this->modifiedFields = [];
    }

    /**
     * @return array
     */
    private function getAttributesForUpdate()
    {
        $attributes = $this->filterAttributesPreserveNull($this->attributes);
        return array_intersect_key($attributes, array_flip($this->modifiedFields));
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->attributes);
    }

    /**
     * @param string $serialized
     * @return void
     */
    public function unserialize($serialized)
    {
        $result = unserialize($serialized);
        foreach ($result as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return json_encode($this->attributes);
    }
}
