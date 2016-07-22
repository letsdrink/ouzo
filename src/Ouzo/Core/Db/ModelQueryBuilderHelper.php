<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;

class ModelQueryBuilderHelper
{
    public static function extractRelations(Model $root, $relationSelector)
    {
        $relations = array();
        if ($relationSelector instanceof Relation) {
            $relations[] = $relationSelector;
        } else {
            $relationNames = explode('->', $relationSelector);
            $model = $root;
            foreach ($relationNames as $name) {
                $relation = $model->getRelation($name);
                $relations[] = $relation;
                $model = $relation->getRelationModelObject();
            }
        }
        return $relations;
    }

    public static function associateRelationsWithAliases($relations, $aliases)
    {
        $aliases = Arrays::toArray($aliases);
        if (count($relations) < count($aliases)) {
            throw new InvalidArgumentException("More aliases than relations in join");
        }

        $relationWithAliases = array();
        foreach ($relations as $index => $relation) {
            $alias = Arrays::getValue($aliases, $index, Arrays::getValue($aliases, $relation->getName()));
            $relationWithAliases[] = new RelationWithAlias($relation, $alias);
        }
        return $relationWithAliases;
    }

    public static function createModelJoins($fromTable, $relationWithAliases, $type, $on)
    {
        $result = array();
        $field = '';
        $table = $fromTable;
        $fetch = true;

        foreach ($relationWithAliases as $relationWithAlias) {
            $relation = $relationWithAlias->relation;

            $field = $field ? $field . '->' . $relation->getName() : $relation->getName();
            $fetch = $fetch && !$relation->isCollection();
            $modelJoin = new ModelJoin($field, $table, $relation, $relationWithAlias->alias, $type, $on, $fetch);
            $table = $modelJoin->alias();
            $result[] = $modelJoin;
        }
        return $result;
    }


}
