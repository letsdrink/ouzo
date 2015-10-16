<?php

namespace Ouzo\Db;


use Ouzo\Db;
use Ouzo\RelationsCache;
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
            $tableName = Arrays::getValue($params, 'table') ?: Strings::tableize((new ReflectionClass($class))->getShortName());
            $primaryKeyName = Arrays::getValue($params, 'primaryKey', 'id');
            $sequenceName = Arrays::getValue($params, 'sequence', "{$tableName}_{$primaryKeyName}_seq");

            list($fields, $defaults) = self::_extractFieldsAndDefaults($params['fields']);

            $_relations = RelationsCache::getRelations($class, $params, $primaryKeyName);


            $_db = empty($params['db']) ? Db::getInstance() : $params['db'];
            if ($primaryKeyName && !in_array($primaryKeyName, $fields)) {
                $fields[] = $primaryKeyName;
            }
            $_afterSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'afterSave'));
            $_beforeSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'beforeSave'));

            self::$cache[$class] = new ModelDefinition($_db, $tableName, $sequenceName, $primaryKeyName, $fields, $_relations, $_afterSaveCallbacks, $_beforeSaveCallbacks, $defaults);
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
                if (is_callable($value)) {
                    $value = $value();
                }
                $defaults[$fieldKey] = $value;
            }
        }
        return array($newFields, $defaults);
    }
}