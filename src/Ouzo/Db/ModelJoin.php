<?php

namespace Ouzo\Db;

class ModelJoin
{
    private $relation;
    private $alias;

    function __construct($relation, $alias)
    {
        $this->relation = $relation;
        $this->alias = $alias;
    }

    function storeField()
    {
        return $this->destinationField() && !$this->relation->isCollection();
    }

    function destinationField()
    {
        return $this->relation->getName();
    }

    public function alias()
    {
        return $this->alias ? : $this->relation->getRelationModelObject()->getTableName();
    }

    public function getModelObject()
    {
        return $this->relation->getRelationModelObject();
    }

}