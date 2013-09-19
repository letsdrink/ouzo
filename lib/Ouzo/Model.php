<?php
namespace Ouzo;

use Exception;
use InvalidArgumentException;
use Ouzo\Db;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Utilities\Objects;

class Model extends Validatable
{
    private $_db;
    private $_attributes;
    private $_tableName;
    private $_sequenceName;
    private $_primaryKeyName;
    private $_fields;

    public function __construct(array $params)
    {
        $this->_prepareParameters($params);

        $tableName = $params['table'];
        $sequenceName = $params['sequence'];
        $primaryKeyName = $params['primaryKey'];
        $attributes = $params['attributes'];
        $fields = $params['fields'];

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
        if (empty($params['table'])) {
            throw new InvalidArgumentException("Table name is required");
        }
        if (empty($params['sequence'])) {
            $params['sequence'] = '';
        }
        if (empty($params['primaryKey'])) {
            $params['primaryKey'] = '';
        }
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

    public function convert($results)
    {
        $objects = array();
        foreach ($results as $row) {
            $objects[] = static::newInstance($row);
        }
        return $objects;
    }

    public function insert()
    {
        $primaryKey = $this->_primaryKeyName;
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);
        $this->$primaryKey = $this->_db->insert($this->_tableName, $attributes, $this->_sequenceName);
        return $this->$primaryKey;
    }

    public function update()
    {
        $attributes = $this->filterAttributesPreserveNull($this->_attributes);
        $this->_db->update($this->_tableName, $attributes, array($this->_primaryKeyName => $this->getId()));
    }

    public function insertOrUpdate()
    {
        if ($this->isNew()) {
            $this->insert();
        } else {
            $this->update();
        }
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

    private function _findById($value)
    {
        $result = $this->where(array($this->_primaryKeyName => $value))->fetch();
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

    public function _getFields()
    {
        return $this->_fields;
    }

    public static function getFields()
    {
        $model = static::newInstance();
        return $model->_getFields();
    }

    private function _getFieldsWithoutPrimaryKey()
    {
        return array_diff($this->_fields, array($this->_primaryKeyName));
    }

    public static function getFieldsWithoutPrimaryKey()
    {
        $model = static::newInstance();
        return $model->_getFieldsWithoutPrimaryKey();
    }

    public function __get($name)
    {
        return isset($this->_attributes[$name]) ? $this->_attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->_attributes[$name] = $value;
    }

    /**
     * @return static
     */
    public static function newInstance($attributes = array())
    {
        $className = get_called_class();
        return new $className($attributes);
    }

    /**
     * @return Model[]
     */
    static public function all()
    {
        return static::where()->fetchAll();
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function select($columns)
    {
        $modelQueryBuilder = new ModelQueryBuilder(static::newInstance());
        return $modelQueryBuilder->select($columns);
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function join($class, $joinKey, $originalKey = null, $destinationField = null)
    {
        $modelQueryBuilder = new ModelQueryBuilder(static::newInstance());
        return $modelQueryBuilder->join($class, $joinKey, $originalKey, $destinationField);
    }

    /**
     * @return ModelQueryBuilder
     */
    static public function where($params = '', $values = null)
    {
        $obj = static::newInstance();
        $modelQueryBuilder = new ModelQueryBuilder($obj, $obj->_db);
        return $modelQueryBuilder->where($params, $values);
    }

    static public function count($where = null, $bindValues = null)
    {
        return static::newInstance()->where($where, $bindValues)->count();
    }

    /**
     * @return Model[]
     */
    static public function find($where, $whereValues, $orderBy = array(), $limit = 0, $offset = 0)
    {
        return static::newInstance()->where($where, $whereValues)->order($orderBy)->limit($limit)->offset($offset)->fetchAll();
    }

    /**
     * @return static
     */
    static public function findById($value)
    {
        return static::newInstance()->_findById($value);
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

}