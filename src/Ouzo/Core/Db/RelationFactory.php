<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\AutoloadNamespaces;
use Ouzo\MetaModelCache;
use Ouzo\Utilities\Arrays;

class RelationFactory
{
    public static function create($relationType, $relation, $relationParams, $primaryKeyName)
    {
        if ($relationType == 'hasOne') {
            return self::hasOne($relation, $relationParams, $primaryKeyName);
        }
        if ($relationType == 'belongsTo') {
            return self::belongsTo($relation, $relationParams, $primaryKeyName);
        }
        if ($relationType == 'hasMany') {
            return self::hasMany($relation, $relationParams, $primaryKeyName);
        }
        throw new InvalidArgumentException("Invalid relation type: $relationType");
    }

    public static function hasMany($name, $params, $primaryKey)
    {
        self::validateParams($params);

        $localKey = Arrays::getValue($params, 'referencedColumn', $primaryKey);
        $foreignKey = $params['foreignKey'];

        return self::newRelation($name, $localKey, $foreignKey, true, $params);
    }

    public static function hasOne($name, $params, $primaryKey)
    {
        self::validateParams($params);

        $localKey = Arrays::getValue($params, 'referencedColumn', $primaryKey);
        $foreignKey = $params['foreignKey'];

        return self::newRelation($name, $localKey, $foreignKey, false, $params);
    }

    public static function belongsTo($name, $params)
    {
        self::validateParams($params);
        $class = $params['class'];
        $localKey = $params['foreignKey'];
        $foreignKey = Arrays::getValue($params, 'referencedColumn') ? : MetaModelCache::getMetaInstance(AutoloadNamespaces::getModelNamespace() . $class)->getIdName();

        return self::newRelation($name, $localKey, $foreignKey, false, $params);
    }

    public static function inline($params)
    {
        self::validateNotEmpty($params, 'class');
        self::validateNotEmpty($params, 'localKey');
        self::validateNotEmpty($params, 'foreignKey');

        $collection = Arrays::getValue($params, 'collection', false);
        $destinationField = Arrays::getValue($params, 'destinationField');

        return self::newRelation($destinationField, $params['localKey'], $params['foreignKey'], $collection, $params);
    }

    private static function validateParams(array $params)
    {
        self::validateNotEmpty($params, 'foreignKey');
        self::validateNotEmpty($params, 'class');
    }

    private static function validateNotEmpty(array $params, $parameter)
    {
        if (empty($params[$parameter])) {
            throw new InvalidArgumentException($parameter . " is required");
        }
    }

    private static function newRelation($name, $localKey, $foreignKey, $collection, $params)
    {
        $class = $params['class'];
        $condition = Arrays::getValue($params, 'conditions', '');
        $order = Arrays::getValue($params, 'order', '');
        return new Relation($name, $class, $localKey, $foreignKey, $collection, $condition, $order);
    }
}
