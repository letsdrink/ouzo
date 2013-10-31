<?php
namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Objects;

class RelationFetcher
{
    private $_relation;

    function __construct(Relation $relation)
    {
        $this->_relation = $relation;
    }

    public function transform(&$results)
    {
        $localKeys = FluentArray::from($results)
            ->map(Functions::extractField($this->_relation->getLocalKey()))
            ->filter(Functions::notEmpty())
            ->unique()
            ->toArray();

        $relationObjectsById = $this->_loadRelationObjectsIndexedById($localKeys);

        foreach ($results as $result) {
            $destinationField = $this->_relation->getName();
            $localKeyValue = Objects::getValue($result, $this->_relation->getLocalKey(), null);
            $values = $this->_findRelationObject($relationObjectsById, $localKeyValue);
            $result->$destinationField = $this->_relation->extractValue($values);
        }
    }

    private function _loadRelationObjectsIndexedById($localKeys)
    {
        $relationObject = $this->_relation->getRelationModelObject();
        $relationObjects = $relationObject::where(array($this->_relation->getForeignKey() => $localKeys))->fetchAll();
        return Arrays::groupBy($relationObjects, Functions::extractField($this->_relation->getForeignKey()));
    }

    private function _findRelationObject($relationObjectsById, $localKey)
    {
        return Arrays::getValue($relationObjectsById, $localKey, array());
    }
}