<?php

namespace Ouzo\Db;


use Ouzo\Db;
use Ouzo\Relations;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class ModelDefinition
{
    /**
     * @var Db
     */
    public $_db;
    public $_tableName;
    public $_sequenceName;
    public $_primaryKeyName;
    public $_fields;
    /**
     * @var Relations
     */
    public $_relations;
    public $_afterSaveCallbacks = array();
    public $_beforeSaveCallbacks = array();
    public $_defaults = array();

    private static $cache = array();

    public function __construct(Db $_db, $_tableName, $_sequenceName, $_primaryKeyName, $_fields, $_relations, array $_afterSaveCallbacks, array $_beforeSaveCallbacks, $_defaults)
    {
        $this->_db = $_db;
        $this->_tableName = $_tableName;
        $this->_sequenceName = $_sequenceName;
        $this->_primaryKeyName = $_primaryKeyName;
        $this->_fields = $_fields;
        $this->_relations = $_relations;
        $this->_afterSaveCallbacks = $_afterSaveCallbacks;
        $this->_beforeSaveCallbacks = $_beforeSaveCallbacks;
        $this->_defaults = $_defaults;
    }

    public static function resetCache()
    {
        self::$cache = array();
    }

    /**
     * @param $class
     * @param $params
     * @return ModelDefinition
     */
    public static function get($class, $params)
    {
        if (!isset(self::$cache[$class])) {
            self::$cache[$class] = self::_createDefinition($class, $params);
        }
        return self::$cache[$class];
    }

    private static function _extractFieldsAndDefaults($fields)
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
                $defaults[$fieldKey] = $value;
            }
        }
        return array($newFields, $defaults);
    }

    public function mergeWithDefaults($attributes)
    {
        $defaults = $this->_defaults;
        foreach ($this->_defaults as $field => $value) {
            if (is_callable($value)) {
                $defaults[$field] = $value();
            }
        }
        return array_replace($defaults, $attributes);
        //return array_merge($defaults, $attributes);
    }

    private static function defaultTable($class)
    {
        $reflectionClass = (new ReflectionClass($class));
        return Strings::tableize($reflectionClass->getShortName());
    }

    /**
     * @param $class
     * @param $params
     * @return ModelDefinition
     */
    private static function _createDefinition($class, $params)
    {
        $tableName = Arrays::getValue($params, 'table') ?: self::defaultTable($class);
        $primaryKeyName = Arrays::getValue($params, 'primaryKey', 'id');
        $sequenceName = Arrays::getValue($params, 'sequence', "{$tableName}_{$primaryKeyName}_seq");

        list($fields, $defaults) = self::_extractFieldsAndDefaults($params['fields']);

        $_relations = new Relations($class, $params, $primaryKeyName);

        $_db = empty($params['db']) ? Db::getInstance() : $params['db'];
        if ($primaryKeyName && !in_array($primaryKeyName, $fields)) {
            $fields[] = $primaryKeyName;
        }
        $_afterSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'afterSave'));
        $_beforeSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'beforeSave'));

        $modelDefinition = new ModelDefinition($_db, $tableName, $sequenceName, $primaryKeyName, $fields, $_relations, $_afterSaveCallbacks, $_beforeSaveCallbacks, $defaults);
        return $modelDefinition;
    }
}