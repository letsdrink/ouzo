<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;

class ModelResultSetConverter
{
    private Model $metaInstance;
    private string $alias;
    /** @var ModelJoin[] */
    private array $joinedModels;
    /** @var RelationToFetch[] */
    private array $relationsToFetch;

    /**
     * @param ModelJoin[] $joinedModels
     * @param RelationToFetch[] $relationsToFetch
     */
    public function __construct(Model $metaInstance, string $alias, array $joinedModels, array $relationsToFetch)
    {
        $this->metaInstance = $metaInstance;
        $this->alias = $alias;
        $this->joinedModels = $joinedModels;
        $this->relationsToFetch = $relationsToFetch;
    }

    /** @return Model[] */
    public function convert(array $results): array
    {
        $aliasToOffset = $this->createAliasToOffsetMap();
        $joinsToStore = FluentArray::from($this->joinedModels)
            ->filter(Functions::extract()->storeField())
            ->uniqueBy(Functions::extract()->destinationField())
            ->toArray();

        $models = Arrays::map($results, fn($row) => $this->convertRowToModel($row, $aliasToOffset, $joinsToStore));
        return $this->fetchRelations($models, $joinsToStore);
    }

    private function createAliasToOffsetMap(): array
    {
        $aliasToOffset = [];

        $aliasToOffset[$this->alias] = 0;
        $offset = count($this->metaInstance->getFields());
        foreach ($this->joinedModels as $joinedModel) {
            if ($joinedModel->storeField()) {
                $aliasToOffset[$joinedModel->alias()] = $offset;
                $offset += count($joinedModel->getModelObject()->getFields());
            }
        }
        return $aliasToOffset;
    }

    /** @param ModelJoin[] $joinsToStore */
    private function convertRowToModel(array $row, array $aliasToOffset, array $joinsToStore): ?Model
    {
        $model = $this->extractModelFromResult($this->metaInstance, $row, $aliasToOffset[$this->alias]);

        foreach ($joinsToStore as $joinedModel) {
            if ($joinedModel->storeField()) {
                $instance = $this->extractModelFromResult($joinedModel->getModelObject(), $row, $aliasToOffset[$joinedModel->alias()]);
                Objects::setValueRecursively($model, $joinedModel->destinationField(), $instance);
            }
        }
        return $model;
    }

    private function extractModelFromResult(Model $metaInstance, array $row, int $offsetInResultSet): ?Model
    {
        $attributes = [];
        $offset = $offsetInResultSet;
        $hasAnyNonEmptyAttribute = false;
        foreach ($metaInstance->_getFields() as $field) {
            $attributes[$field] = $row[$offset];
            $hasAnyNonEmptyAttribute = $hasAnyNonEmptyAttribute || $row[$offset];
            $offset++;
        }
        return $hasAnyNonEmptyAttribute ? $metaInstance->newInstance($attributes) : null;
    }

    /**
     * @param Model[] $results
     * @param ModelJoin[] $joinsToStore
     * @return Model[]
     */
    private function fetchRelations(array $results, array $joinsToStore): array
    {
        $joinedRelations = Arrays::map($joinsToStore, Functions::extract()->destinationField());
        foreach ($this->relationsToFetch as $relationToFetch) {
            if (!in_array($relationToFetch->destinationField, $joinedRelations)) {
                $relationFetcher = new RelationFetcher($relationToFetch->relation);
                $fieldTransformer = new FieldTransformer($relationToFetch->field, $relationFetcher);
                $fieldTransformer->transform($results);
            }
        }
        return $results;
    }
}
