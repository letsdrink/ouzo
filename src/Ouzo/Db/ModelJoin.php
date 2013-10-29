<?php

namespace Ouzo\Db;

class ModelJoin
{
    private $relation;
    private $alias;
    private $destinationField;

    function __construct($destinationField, $relation, $alias)
    {
        $this->relation = $relation;
        $this->alias = $alias;
        $this->destinationField = $destinationField;
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

    public function asJoinClause($fromTable)
    {
        $joinedModel = $this->relation->getRelationModelObject();
        $joinTable = $joinedModel->getTableName();
        $joinKey = $this->relation->getForeignKey();
        $idName = $this->relation->getLocalKey();
        return new JoinClause($joinTable, $joinKey, $idName, $fromTable, $this->alias);
    }
}