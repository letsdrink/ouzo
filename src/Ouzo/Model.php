<?php
namespace Ouzo;

use Exception;
use InvalidArgumentException;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Relation;
use Ouzo\Db;
use Ouzo\Db\RelationFetcher;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use PDO;
use ReflectionClass;

class Model extends Validatable
{
    private $_db;
    private $_attributes;
    private $_tableName;
    private $_sequenceName;
    private $_primaryKeyName;
    private $_fields;
    private $_relations;

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
     * 'fields' - mapped column names
     * 'attributes' -  array of column => value
     * </code>
     */

    public function __construct(array $params)
    {
        $this->_prepareParameters($params);

        $tableName = Arrays::getValue($params, 'table') ? : Strings::tableize($this->getModelName());
        $primaryKeyName = Arrays::getValue($params, 'primaryKey', 'id');
        $sequenceName = Arrays::getValue($params, 'sequence', "{$tableName}_{$primaryKeyName}_seq");

        $attributes = $params['attributes'];
        $fields = $params['fields'];

        $this->_relations = new Relations(get_called_class(), $params, $primaryKeyName);

        if (isset($attributes[$primaryKeyName]) && !$attributes[$primaryKeyName]) unset($attributes[$primaryKeyName]);

        $this->_tableName = $tableName;
        $this->_sequenceName = $sequenceName;
        $this->_primaryKeyName = $primaryKeyName;
        $this->_db = empty($params['db']) ? Db::getInstance() : $params['db'];
        $this->_fields = $fields;
        if ($primaryKeyName) {
            $this->_fields[] = $primaryKeyName;
        }
        $this->_attributes = $this->filterAttributes($attributes);
    }

    public function assignAttributes($attributes)
    {
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
        $primaryKey = $this->_primaryKeyName;
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);
        $value = $this->_db->insert($this->_tableName, $attributes, $this->_sequenceName);
        if ($primaryKey) {
            $this->$primaryKey = $value;
        }
        return $value;
    }

    public function update()
    {
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);
        $this->_db->update($this->_tableName, $attributes, array($this->_primaryKeyName => $this->getId()));
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

    public function __set($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    private function _fetchRelation($name)
    {
        $relation = $this->getRelation($name);
        $relationFetcher = new RelationFetcher($relation);
        $results = array($this);
        $relationFetcher->transform($results);
    }

    /**
     * @return static
     */
    public static function newInstance(array $attributes)
    {
        $className = get_called_class();
        return new $className($attributes);
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
    static public function all()
    {
        return static::queryBuilder()->fetchAll();
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function select($columns, $type = PDO::FETCH_NUM)
    {
        return static::queryBuilder()->select($columns, $type);
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function join($relation, $alias = null)
    {
        return static::queryBuilder()->join($relation, $alias);
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function innerJoin($relation, $alias = null)
    {
        return static::queryBuilder()->innerJoin($relation, $alias);
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function where($params = '', $values = array())
    {
        return static::queryBuilder()->where($params, $values);
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function queryBuilder($alias = null)
    {
        $obj = static::metaInstance();
        return new ModelQueryBuilder($obj, $obj->_db, $alias);
    }

    static public function count($where = null, $bindValues = null)
    {
        return static::metaInstance()->where($where, $bindValues)->count();
    }

    public static function alias($alias)
    {
        return static::queryBuilder($alias);
    }

    /**
     * @return Model[]
     */
    static public function find($where, $whereValues, $orderBy = array(), $limit = 0, $offset = 0)
    {
        return static::metaInstance()->where($where, $whereValues)->order($orderBy)->limit($limit)->offset($offset)->fetchAll();
    }

    /** Executes a native sql and returns an array of model objects created by passing every result row to the model constructor.
     * @param $nativeSql - database specific sql
     * @param array $params - bind parameters
     * @return Model[]
     */
    static public function findBySql($nativeSql, $params = array())
    {
        $meta = static::metaInstance();
        $results = $meta->_db->query($nativeSql, $params)->fetchAll();

        return Arrays::map($results, function($row) use($meta) {
            return $meta->newInstance($row);
        });
    }

    /**
     * @return static
     */
    static public function findById($value)
    {
        return static::metaInstance()->_findById($value);
    }

    /**
     * @return static
     */
    static public function findByIdOrNull($value)
    {
        return static::metaInstance()->_findByIdOrNull($value);
    }

    /**
     * @return static
     */
    static public function create($attributes)
    {
        $instance = static::newInstance($attributes);
        if (!$instance->isValid()) {
            throw new Exception("Validation has failed for object: " . $instance->inspect() . "\nErrors: " . Objects::toString($instance->getErrors()));
        }
        $instance->insert();
        return $instance;
    }

    /**
     * Should be used for tests purposes only.
     *
     * @return static
     */
    static public function createWithoutValidation($attributes)
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

    public function nullifyIfEmpty(&$attributes, $field)
    {

        if (isset($attributes[$field]) && !$attributes[$field]) {
            $attributes[$field] = null;
        }
    }

    public function get($names, $default = null)
    {
        return Objects::getValueRecursively($this, $names, $default);
    }

    /**
     * @return Relation
     */
    public function getRelation($name)
    {
        return $this->_relations->getRelation($name);
    }
}