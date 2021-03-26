<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Db\Query;
use Ouzo\Db\QueryBoundValuesExtractor;

class ExistsClause extends WhereClause
{
    private Query $query;
    private bool $negate;

    public function __construct(Query $query, $negate = false)
    {
        $this->query = $query;
        $this->negate = $negate;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function toSql(): string
    {
        $sql = DialectFactory::create()->buildQuery($this->query);
        return $this->negate ? "NOT EXISTS ({$sql})" : "EXISTS ({$sql})";
    }

    public function getParameters(): array
    {
        $queryBindValuesExtractor = new QueryBoundValuesExtractor($this->query);
        return $queryBindValuesExtractor->extract();
    }
}
