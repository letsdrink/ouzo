<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class RelationFetcher
{
    private $_relation;

    public function __construct(Relation $relation)
    {
        $this->_relation = $relation;
    }

    public function transform(&$results)
    {
        $localKeyName = $this->_relation->getLocalKey();
        $localKeys = FluentArray::from($results)
            ->map(Functions::extractField($localKeyName))
            ->filterNotBlank()
            ->unique()
            ->toArray();

        $relationObjectsById = $localKeys? $this->_loadRelationObjectsIndexedById($localKeys) : array();

        foreach ($results as $result) {
            $values = $this->_findRelationObject($relationObjectsById, $result->$localKeyName);
            $destinationField = $this->_relation->getName();
            $result->$destinationField = $this->_relation->extractValue($values);
        }
    }

    private function _loadRelationObjectsIndexedById($localKeys)
    {
        $relationObject = $this->_relation->getRelationModelObject();
        $relationObjects = $relationObject::where(array($this->_relation->getForeignKey() => $localKeys))
            ->where($this->_relation->getCondition())
            ->order($this->_relation->getOrder())
            ->fetchAll();
        return Arrays::groupBy($relationObjects, Functions::extractField($this->_relation->getForeignKey()));
    }

    private function _findRelationObject($relationObjectsById, $localKey)
    {
        return Arrays::getValue($relationObjectsById, $localKey, array());
    }
}
