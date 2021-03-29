<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\AutoloadNamespaces;
use Ouzo\MetaModelCache;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class RelationFactory
{
    public static function create(string $relationType, string $relation, array|string $relationParams, string $primaryKeyName, string $modelClass): Relation
    {
        if (is_string($relationParams)) {
            $relationParams = ['class' => $relationParams];
        }

        if ($relationType == 'hasOne') {
            return self::hasOne($relation, $relationParams, $primaryKeyName, $modelClass);
        }
        if ($relationType == 'belongsTo') {
            return self::belongsTo($relation, $relationParams);
        }
        if ($relationType == 'hasMany') {
            return self::hasMany($relation, $relationParams, $primaryKeyName, $modelClass);
        }
        throw new InvalidArgumentException("Invalid relation type: $relationType");
    }

    public static function hasMany(string $name, array $params, string $primaryKey, string $modelClass): Relation
    {
        self::validateParams($params);

        $localKey = Arrays::getValue($params, 'referencedColumn', $primaryKey);
        $foreignKey = Arrays::getValue($params, 'foreignKey', self::defaultForeignKey($modelClass));

        return self::newRelation($name, $localKey, $foreignKey, true, $params);
    }

    public static function hasOne(string $name, array $params, string $primaryKey, string $modelClass): Relation
    {
        self::validateParams($params);

        $localKey = Arrays::getValue($params, 'referencedColumn', $primaryKey);
        $foreignKey = Arrays::getValue($params, 'foreignKey', self::defaultForeignKey($modelClass));

        return self::newRelation($name, $localKey, $foreignKey, false, $params);
    }

    public static function belongsTo(string $name, array $params): Relation
    {
        self::validateParams($params);
        $class = $params['class'];
        $localKey = Arrays::getValue($params, 'foreignKey', self::defaultForeignKey($class));
        $foreignKey = Arrays::getValue($params, 'referencedColumn') ?: MetaModelCache::getMetaInstance(AutoloadNamespaces::getModelNamespace() . $class)->getIdName();

        return self::newRelation($name, $localKey, $foreignKey, false, $params);
    }

    public static function inline(array $params): Relation
    {
        self::validateNotEmpty($params, 'class');
        self::validateNotEmpty($params, 'localKey');
        self::validateNotEmpty($params, 'foreignKey');

        $collection = Arrays::getValue($params, 'collection', false);
        $destinationField = Arrays::getValue($params, 'destinationField');

        return self::newRelation($destinationField, $params['localKey'], $params['foreignKey'], $collection, $params);
    }

    private static function validateParams(array $params): void
    {
        self::validateNotEmpty($params, 'class');
    }

    private static function defaultForeignKey(string $modelClass): string
    {
        return Strings::camelCaseToUnderscore(Arrays::last(explode('\\', $modelClass))) . '_id';
    }

    private static function validateNotEmpty(array $params, string $parameter): void
    {
        if (empty($params[$parameter])) {
            throw new InvalidArgumentException("{$parameter} is required");
        }
    }

    private static function newRelation(?string $name, string $localKey, string $foreignKey, bool $collection, array $params): Relation
    {
        $class = $params['class'];
        $condition = Arrays::getValue($params, 'conditions', '');
        $order = Arrays::getValue($params, 'order', '');
        return new Relation($name, $class, $localKey, $foreignKey, $collection, $condition, $order);
    }
}
