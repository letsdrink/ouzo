<?php
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

        $relationObjectsById = $this->_loadRelationObjectsIndexedById($localKeys);

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
            ->fetchAll();
        return Arrays::groupBy($relationObjects, Functions::extractField($this->_relation->getForeignKey()));
    }

    private function _findRelationObject($relationObjectsById, $localKey)
    {
        return Arrays::getValue($relationObjectsById, $localKey, array());
    }
}