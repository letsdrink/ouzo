<?php
namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\Model;
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
        $foreignKeys = FluentArray::from($results)
            ->map(Functions::extractFieldRecursively($this->_relation->getLocalKey()))
            ->filter(Functions::notEmpty())
            ->unique()
            ->toArray();

        $relationObjectsById = $this->_loadRelationObjectsIndexedById($foreignKeys);

        foreach ($results as $result) {
            $destinationField = $this->_relation->getName();
            $foreignKeyValue = Objects::getValueRecursively($result, $this->_relation->getLocalKey());
            if ($foreignKeyValue) {
                $values = $this->_findRelationObject($result, $relationObjectsById, $foreignKeyValue);

                $result->$destinationField = $this->_relation->extractValue($values);
            }
        }
    }

    private function _loadRelationObjectsIndexedById($foreignKeys)
    {
        $relationObject = $this->_relation->getRelationModelObject();
        $relationObjects = $relationObject::where(array($this->_relation->getForeignKey() => $foreignKeys))->fetchAll();
        return Arrays::groupBy($relationObjects, Functions::extractField($this->_relation->getForeignKey()));
    }

    private function _findRelationObject(Model $result, $relationObjectsById, $foreignKey)
    {
        if (!isset($relationObjectsById[$foreignKey])) {
            if ($this->_relation->getAllowInvalidReferences()) {
                return array();
            }
            throw new InvalidArgumentException("Cannot find {$this->_relation->getClass()} with {$this->_relation->getForeignKey()} = $foreignKey for {$result->getModelName()} with id = {$result->getId()}");
        }
        return $relationObjectsById[$foreignKey];
    }
}