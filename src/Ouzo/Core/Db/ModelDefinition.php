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
    public $db;
    public $table;
    public $sequence;
    public $primaryKey;
    public $fields;
    /**
     * @var Relations
     */
    public $relations;
    public $afterSaveCallbacks = array();
    public $beforeSaveCallbacks = array();
    public $defaults = array();

    private static $cache = array();

    public function __construct(Db $db, $table, $sequence, $primaryKey, $fields, $relations, array $afterSaveCallbacks, array $beforeSaveCallbacks, $defaults)
    {
        $this->db = $db;
        $this->table = $table;
        $this->sequence = $sequence;
        $this->primaryKey = $primaryKey;
        $this->fields = $fields;
        $this->relations = $relations;
        $this->afterSaveCallbacks = $afterSaveCallbacks;
        $this->beforeSaveCallbacks = $beforeSaveCallbacks;
        $this->defaults = $defaults;
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

    public function mergeWithDefaults($attributes)
    {
        $defaults = $this->defaults;
        foreach ($this->defaults as $field => $value) {
            if (is_callable($value)) {
                $defaults[$field] = $value();
            }
        }
        return array_replace($defaults, $attributes);
    }

    private static function defaultTable($class)
    {
        $reflectionClass = new ReflectionClass($class);
        return Strings::tableize($reflectionClass->getShortName());
    }

    /**
     * @param $class
     * @param $params
     * @return ModelDefinition
     */
    private static function _createDefinition($class, $params)
    {
        $table = Arrays::getValue($params, 'table') ?: self::defaultTable($class);
        $primaryKey = Arrays::getValue($params, 'primaryKey', 'id');
        $sequence = Arrays::getValue($params, 'sequence', "{$table}_{$primaryKey}_seq");

        list($fields, $defaults) = self::_extractFieldsAndDefaults($params['fields']);

        $relations = new Relations($class, $params, $primaryKey);

        $db = empty($params['db']) ? Db::getInstance() : $params['db'];
        if ($primaryKey && !in_array($primaryKey, $fields)) {
            $fields[] = $primaryKey;
        }
        $afterSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'afterSave'));
        $beforeSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'beforeSave'));

        return new ModelDefinition($db, $table, $sequence, $primaryKey, $fields, $relations, $afterSaveCallbacks, $beforeSaveCallbacks, $defaults);
    }

    private static function _extractFieldsAndDefaults($fields)
    {
        $newFields = array();
        $defaults = array();
        foreach ($fields as $key => $value) {
            if (is_numeric($key)) {
                $newFields[] = $value;
            } else {
                $newFields[] = $key;
                $defaults[$key] = $value;
            }
        }
        return array($newFields, $defaults);
    }
}