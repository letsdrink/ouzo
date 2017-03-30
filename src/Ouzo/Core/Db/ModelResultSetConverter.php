<?php
namespace Ouzo\Db;

use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;

class ModelResultSetConverter
{
    /** @var Model */
    private $metaInstance;
    /** @var string */
    private $alias;
    /** @var ModelJoin[] */
    private $joinedModels;
    /** @var RelationToFetch[] */
    private $relationsToFetch;

    /**
     * @param Model $metaInstance
     * @param string $alias
     * @param ModelJoin[] $joinedModels
     * @param RelationToFetch[] $relationsToFetch
     */
    public function __construct($metaInstance, $alias, array $joinedModels, array $relationsToFetch)
    {
        $this->metaInstance = $metaInstance;
        $this->alias = $alias;
        $this->joinedModels = $joinedModels;
        $this->relationsToFetch = $relationsToFetch;
    }

    /**
     * @param array $results
     * @return Model[]
     */
    public function convert($results)
    {
        $aliasToOffset = $this->createAliasToOffsetMap();
        $joinsToStore = FluentArray::from($this->joinedModels)
            ->filter(Functions::extract()->storeField())
            ->uniqueBy(Functions::extract()->destinationField())
            ->toArray();

        $models = [];
        foreach ($results as $row) {
            $models[] = $this->convertRowToModel($row, $aliasToOffset, $joinsToStore);
        }
        return $this->fetchRelations($models, $joinsToStore);
    }

    /**
     * @return array
     */
    private function createAliasToOffsetMap()
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

    /**
     * @param array $row
     * @param array $aliasToOffset
     * @param ModelJoin[] $joinsToStore
     * @return Model
     */
    private function convertRowToModel(array $row, $aliasToOffset, $joinsToStore)
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

    /**
     * @param Model $metaInstance
     * @param array $row
     * @param mixed $offsetInResultSet
     * @return Model|null
     */
    private function extractModelFromResult($metaInstance, array $row, $offsetInResultSet)
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
    private function fetchRelations($results, $joinsToStore)
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
