<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;


use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Db\Query;
use Ouzo\Db\QueryBoundValuesExtractor;

class ExistsClause extends WhereClause
{
    /** @var Query */
    private $query;

    /** @var bool */
    private $negate;

    public function __construct(Query $query, $negate = false)
    {
        $this->query = $query;
        $this->negate = $negate;
    }

    public function isEmpty()
    {
        return false;
    }

    public function toSql()
    {
        $sql = DialectFactory::create()->buildQuery($this->query);
        return $this->negate ? "NOT EXISTS ($sql)" : "EXISTS ($sql)";
    }

    public function getParameters()
    {
        $queryBindValuesExtractor = new QueryBoundValuesExtractor($this->query);
        return $queryBindValuesExtractor->extract();
    }
}
