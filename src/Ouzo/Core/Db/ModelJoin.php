<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Model;

class ModelJoin
{
    /** @var Relation */
    private $relation;
    /** @var string */
    private $alias;
    /** @var string */
    private $destinationField;
    /** @var string */
    private $fromTable;
    /** @var string */
    private $type;
    /** @var string */
    private $on;
    /** @var bool */
    private $fetch;

    /**
     * @param string $destinationField
     * @param string $fromTable
     * @param string $relation
     * @param string $alias
     * @param string $type
     * @param string $on
     * @param bool $fetch
     */
    public function __construct($destinationField, $fromTable, $relation, $alias, $type, $on, $fetch)
    {
        $this->relation = $relation;
        $this->alias = $alias;
        $this->destinationField = $destinationField;
        $this->fromTable = $fromTable;
        $this->type = $type;
        $this->on = $on;
        $this->fetch = $fetch;
    }

    /**
     * @return bool
     */
    public function storeField()
    {
        return $this->fetch &&  $this->destinationField();
    }

    /**
     * @return string
     */
    public function destinationField()
    {
        return $this->destinationField;
    }

    /**
     * @return string
     */
    public function alias()
    {
        return $this->alias ? : $this->relation->getRelationModelObject()->getTableName();
    }

    /**
     * @return Model
     */
    public function getModelObject()
    {
        return $this->relation->getRelationModelObject();
    }

    /**
     * @return JoinClause
     */
    public function asJoinClause()
    {
        $joinedModel = $this->relation->getRelationModelObject();
        $joinTable = $joinedModel->getTableName();
        $joinKey = $this->relation->getForeignKey();
        $idName = $this->relation->getLocalKey();
        $onClauses = [WhereClause::create($this->on), $this->relation->getCondition()];
        return new JoinClause($joinTable, $joinKey, $idName, $this->fromTable, $this->alias, $this->type, $onClauses);
    }

    /**
     * @param ModelJoin $other
     * @return bool
     */
    public function equals(ModelJoin $other)
    {
        return
            $this->relation === $other->relation &&
            $this->alias === $other->alias &&
            $this->destinationField === $other->destinationField &&
            $this->fromTable === $other->fromTable &&
            $this->type === $other->type &&
            $this->on === $other->on;
    }

    /**
     * @param $other
     * @return \Closure
     */
    public static function equalsPredicate($other)
    {
        return function ($modelJoin) use ($other) {
            return $modelJoin->equals($other);
        };
    }
}
