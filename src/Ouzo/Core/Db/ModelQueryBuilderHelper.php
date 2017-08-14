<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\FluentFunctions;
use Ouzo\Utilities\Functions;

class ModelQueryBuilderHelper
{
    /**
     * @param Model $root
     * @param string|Relation $relationSelector
     * @param ModelJoin[] $joins
     * @return Relation[]
     */
    public static function extractRelations(Model $root, $relationSelector, $joins = [])
    {
        $relations = [];
        if ($relationSelector instanceof Relation) {
            $relations[] = $relationSelector;
        } else {
            $relationNames = explode('->', $relationSelector);
            $model = $root;
            foreach ($relationNames as $name) {
                $relation = self::getRelation($model, $name, $joins);
                $relations[] = $relation;
                $model = $relation->getRelationModelObject();
            }
        }
        return $relations;
    }

    /**
     * @param Relation[] $relations
     * @param string|string[]|null $aliases
     * @return RelationWithAlias[]
     */
    public static function associateRelationsWithAliases(array $relations, $aliases)
    {
        $aliases = Arrays::toArray($aliases);
        if (count($relations) < count($aliases)) {
            throw new InvalidArgumentException("More aliases than relations in join");
        }

        $relationWithAliases = [];
        foreach ($relations as $index => $relation) {
            $alias = Arrays::getValue($aliases, $index, Arrays::getValue($aliases, $relation->getName()));
            $relationWithAliases[] = new RelationWithAlias($relation, $alias);
        }
        return $relationWithAliases;
    }

    /**
     * @param string $fromTable
     * @param RelationWithAlias[] $relationWithAliases
     * @param string $type
     * @param string $on
     * @return ModelJoin[]
     */
    public static function createModelJoins($fromTable, $relationWithAliases, $type, $on)
    {
        $result = [];
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

    /**
     * @param Model $model
     * @param string $name
     * @param ModelJoin[] $joins
     * @return Relation|null
     */
    private static function getRelation($model, $name, $joins)
    {
        return $model->hasRelation($name) ? $model->getRelation($name) : self::getRelationForInlineJoin($name, $joins);
    }

    private static function getRelationForInlineJoin($name, $joins)
    {
        return FluentArray::from($joins)
            ->map(Functions::extractExpression('getRelation()'))
            ->filter(FluentFunctions::extractExpression('getName()')->equals($name))
            ->firstOr(null);
    }
}
