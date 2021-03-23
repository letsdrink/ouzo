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
     * @param ModelJoin[] $joins
     * @return Relation[]
     */
    public static function extractRelations(Model $root, Relation|string $relationSelector, array $joins = []): array
    {
        if ($relationSelector instanceof Relation) {
            return [$relationSelector];
        }

        $relations = [];
        $relationNames = explode('->', $relationSelector);
        $model = $root;
        foreach ($relationNames as $name) {
            $relation = self::getRelation($model, $name, $joins);
            $relations[] = $relation;
            $model = $relation->getRelationModelObject();
        }
        return $relations;
    }

    /**
     * @param Relation[] $relations
     * @return RelationWithAlias[]
     */
    public static function associateRelationsWithAliases(array $relations, array|string|null $aliases): array
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
     * @param RelationWithAlias[] $relationWithAliases
     * @return ModelJoin[]
     */
    public static function createModelJoins(string $fromTable, array $relationWithAliases, string $type, array $on): array
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

    /** @param ModelJoin[] $joins */
    private static function getRelation(Model $model, string $name, array $joins): ?Relation
    {
        return $model->hasRelation($name) ? $model->getRelation($name) : self::getRelationForInlineJoin($name, $joins);
    }

    /** @param ModelJoin[] $joins */
    private static function getRelationForInlineJoin(string $name, array $joins)
    {
        return FluentArray::from($joins)
            ->map(Functions::extractExpression('getRelation()'))
            ->filter(FluentFunctions::extractExpression('getName()')->equals($name))
            ->firstOr(null);
    }
}
