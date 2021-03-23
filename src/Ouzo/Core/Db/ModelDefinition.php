<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db;
use Ouzo\Relations;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class ModelDefinition
{
    private static array $cache = [];

    public function __construct(
        public Db $db,
        public string $table,
        public string $sequence,
        public string $primaryKey,
        public string|array $fields,
        public Relations $relations,
        public array $afterSaveCallbacks,
        public array $beforeSaveCallbacks,
        public array $defaults
    )
    {
    }

    public static function resetCache(): void
    {
        self::$cache = [];
    }

    public static function get(string $class, array $params): ModelDefinition
    {
        if (!isset(self::$cache[$class])) {
            self::$cache[$class] = self::createDefinition($class, $params);
        }
        return self::$cache[$class];
    }

    public function mergeWithDefaults(array $attributes, array $fields): array
    {
        if (empty($this->defaults)) {
            return $attributes;
        }
        $defaultsToUse = array_diff_key(array_intersect_key($fields, $this->defaults), $attributes);
        foreach ($defaultsToUse as $field => $value) {
            if (is_callable($value)) {
                $attributes[$field] = $value();
            } else {
                $attributes[$field] = $value;
            }
        }
        return $attributes;
    }

    private static function defaultTable(string $class): string
    {
        $reflectionClass = new ReflectionClass($class);
        return Strings::tableize($reflectionClass->getShortName());
    }

    private static function createDefinition(string $class, array $params): ModelDefinition
    {
        $table = Arrays::getValue($params, 'table') ?: self::defaultTable($class);
        $primaryKey = Arrays::getValue($params, 'primaryKey', 'id');
        $sequence = Arrays::getValue($params, 'sequence', "{$table}_{$primaryKey}_seq");

        list($fields, $defaults) = self::extractFieldsAndDefaults($params['fields']);

        $relations = new Relations($class, $params, $primaryKey);

        $db = empty($params['db']) ? Db::getInstance() : $params['db'];
        if ($primaryKey && !in_array($primaryKey, $fields)) {
            $fields[] = $primaryKey;
        }
        $afterSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'afterSave'));
        $beforeSaveCallbacks = Arrays::toArray(Arrays::getValue($params, 'beforeSave'));

        return new ModelDefinition($db, $table, $sequence, $primaryKey, $fields, $relations, $afterSaveCallbacks, $beforeSaveCallbacks, $defaults);
    }

    private static function extractFieldsAndDefaults(array $fields): array
    {
        $newFields = [];
        $defaults = [];
        foreach ($fields as $key => $value) {
            if (is_numeric($key)) {
                $newFields[] = $value;
            } else {
                $newFields[] = $key;
                $defaults[$key] = $value;
            }
        }
        return [$newFields, $defaults];
    }
}
