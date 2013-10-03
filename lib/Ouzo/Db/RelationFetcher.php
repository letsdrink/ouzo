<?php
namespace Ouzo\Db;

use InvalidArgumentException;
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
            ->map(Functions::extractFieldRecursively($this->_relation->getForeignKey()))
            ->filter(Functions::notEmpty())
            ->unique()
            ->toArray();

        $relationObjectsById = $this->_loadRelationObjectsIndexedById($foreignKeys);

        foreach ($results as $result) {
            $destinationField = $this->_relation->getName();
            $foreignKey = Objects::getValueRecursively($result, $this->_relation->getForeignKey());
            if ($foreignKey) {
                $result->$destinationField = $this->_findRelationObject($result, $relationObjectsById, $foreignKey);
            }
        }
    }

    private function _loadRelationObjectsIndexedById($foreignKeys)
    {
        $relationObject = $this->_relation->getRelationModelObject();
        $relationObjects = $relationObject::where(array($this->_relation->getReferencedColumn() => $foreignKeys))->fetchAll();
        return Arrays::toMap($relationObjects, Functions::extractField($this->_relation->getReferencedColumn()));
    }

    private function _findRelationObject($result, $relationObjectsById, $foreignKey)
    {
        if (!isset($relationObjectsById[$foreignKey])) {
            if ($this->_relation->getAllowInvalidReferences()) {
                return null;
            }
            throw new InvalidArgumentException("Cannot find {$this->_relation->getClass()} with {$this->_relation->getReferencedColumn()} = $foreignKey for {$result->inspect()}");
        }
        return $relationObjectsById[$foreignKey];
    }
}