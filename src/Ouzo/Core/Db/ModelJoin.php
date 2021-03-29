<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Closure;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Model;

class ModelJoin
{
    public function __construct(
        private string $destinationField,
        private string $fromTable,
        private Relation $relation,
        private ?string $alias,
        private string $type,
        private array|string $on,
        private bool $fetch
    )
    {
    }

    public function getRelation(): Relation
    {
        return $this->relation;
    }

    public function storeField(): bool
    {
        return $this->fetch && $this->destinationField();
    }

    public function destinationField(): string
    {
        return $this->destinationField;
    }

    public function alias(): string
    {
        return $this->alias ?: $this->relation->getRelationModelObject()->getTableName();
    }

    public function getModelObject(): Model
    {
        return $this->relation->getRelationModelObject();
    }

    public function asJoinClause(): JoinClause
    {
        $joinedModel = $this->relation->getRelationModelObject();
        $joinTable = $joinedModel->getTableName();
        $joinKey = $this->relation->getForeignKey();
        $idName = $this->relation->getLocalKey();
        $onClauses = [WhereClause::create($this->on), $this->relation->getCondition()];
        return new JoinClause($joinTable, $joinKey, $idName, $this->fromTable, $this->alias, $this->type, $onClauses);
    }

    public function equals(ModelJoin $other): bool
    {
        return
            $this->relation === $other->relation &&
            $this->alias === $other->alias &&
            $this->destinationField === $other->destinationField &&
            $this->fromTable === $other->fromTable &&
            $this->type === $other->type &&
            $this->on === $other->on;
    }

    public static function equalsPredicate($other): Closure
    {
        return fn($modelJoin) => $modelJoin->equals($other);
    }
}
