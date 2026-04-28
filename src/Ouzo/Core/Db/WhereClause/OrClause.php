<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

use Ouzo\Db\Dialect\DialectUtil;
use Ouzo\Utilities\Arrays;

class OrClause extends WhereClause
{
    /** @var WhereClause[] */
    private array $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    #[Override]
    public function isEmpty(): bool
    {
        return Arrays::all($this->conditions, fn(WhereClause $where) => $where->isEmpty());
    }

    #[Override]
    public function isNeverSatisfied(): bool
    {
        return Arrays::all($this->conditions, fn(WhereClause $where) => $where->isNeverSatisfied());
    }

    #[Override]
    public function toSql(): string
    {
        return DialectUtil::joinClauses($this->conditions, 'OR', fn(WhereClause $where) => $where->toSql());
    }

    #[Override]
    public function getParameters(): array
    {
        return Arrays::concat(Arrays::map($this->conditions, fn(WhereClause $where) => $where->getParameters()));
    }
}
