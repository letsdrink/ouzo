<?php
namespace Ouzo\Db;

class ModelJoin
{
    private $relation;
    private $alias;
    private $destinationField;
    private $fromTable;

    function __construct($destinationField, $fromTable, $relation, $alias)
    {
        $this->relation = $relation;
        $this->alias = $alias;
        $this->destinationField = $destinationField;
        $this->fromTable = $fromTable;
    }

    function storeField()
    {
        return $this->destinationField() && !$this->relation->isCollection();
    }

    function destinationField()
    {
        return $this->destinationField;
    }

    public function alias()
    {
        return $this->alias ? : $this->relation->getRelationModelObject()->getTableName();
    }

    public function getModelObject()
    {
        return $this->relation->getRelationModelObject();
    }

    public function asJoinClause()
    {
        $joinedModel = $this->relation->getRelationModelObject();
        $joinTable = $joinedModel->getTableName();
        $joinKey = $this->relation->getForeignKey();
        $idName = $this->relation->getLocalKey();
        return new JoinClause($joinTable, $joinKey, $idName, $this->fromTable, $this->alias);
    }

    public function equals(ModelJoin $other)
    {
        return
            $this->relation === $other->relation &&
            $this->alias === $other->alias &&
            $this->destinationField === $other->destinationField &&
            $this->fromTable === $other->fromTable;
    }

    public static function equalsPredicate($other)
    {
        return function ($modelJoin) use($other) {
            return $modelJoin->equals($other);
        };
    }
}