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

class RelationFetcher
{
    public function __construct(private Relation $relation)
    {
    }

    /** @var Model[] $results */
    public function transform(array $results)
    {
        $localKeyName = $this->relation->getLocalKey();
        $localKeys = FluentArray::from($results)
            ->map(Functions::extractField($localKeyName))
            ->filterNotBlank()
            ->unique()
            ->toArray();

        $relationObjectsById = $localKeys ? $this->loadRelationObjectsIndexedById($localKeys) : [];

        foreach ($results as $result) {
            $values = $this->findRelationObject($relationObjectsById, $result->$localKeyName);
            $destinationField = $this->relation->getName();
            $result->$destinationField = $this->relation->extractValue($values);
        }
    }

    private function loadRelationObjectsIndexedById(array|string $localKeys): array
    {
        $relationObject = $this->relation->getRelationModelObject();
        $relationObjects = $relationObject::where([$this->relation->getForeignKey() => $localKeys])
            ->where($this->relation->getCondition())
            ->order($this->relation->getOrder())
            ->fetchAll();
        return Arrays::groupBy($relationObjects, Functions::extractField($this->relation->getForeignKey()));
    }

    /** @return Model[] */
    private function findRelationObject(array $relationObjectsById, ?string $localKey): array
    {
        return Arrays::getValue($relationObjectsById, $localKey, []);
    }
}
