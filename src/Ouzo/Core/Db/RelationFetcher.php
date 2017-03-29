<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class RelationFetcher
{
    /** @var Relation */
    private $relation;

    /**
     * @param Relation $relation
     */
    public function __construct(Relation $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @param Model $results
     */
    public function transform(&$results)
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

    /**
     * @param string $localKeys
     * @return array
     */
    private function loadRelationObjectsIndexedById($localKeys)
    {
        $relationObject = $this->relation->getRelationModelObject();
        $relationObjects = $relationObject::where([$this->relation->getForeignKey() => $localKeys])
            ->where($this->relation->getCondition())
            ->order($this->relation->getOrder())
            ->fetchAll();
        return Arrays::groupBy($relationObjects, Functions::extractField($this->relation->getForeignKey()));
    }

    /**
     * @param array $relationObjectsById
     * @param string $localKey
     * @return mixed
     */
    private function findRelationObject($relationObjectsById, $localKey)
    {
        return Arrays::getValue($relationObjectsById, $localKey, []);
    }
}
