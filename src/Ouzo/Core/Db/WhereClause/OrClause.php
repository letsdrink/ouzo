<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;


use Ouzo\Db\Dialect\DialectUtil;
use Ouzo\Utilities\Arrays;

class OrClause extends WhereClause
{
    /**
     * @var WhereClause[]
     */
    private $conditions;

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }

    public function isEmpty()
    {
        return Arrays::all($this->conditions, function ($where) {
            return $where->isEmpty();
        });
    }

    public function isNeverSatisfied()
    {
        return Arrays::all($this->conditions, function ($where) {
            return $where->isNeverSatisfied();
        });
    }

    public function toSql()
    {
        return DialectUtil::joinClauses($this->conditions, 'OR', function ($where) {
            return $where->toSql();
        });
    }

    public function getParameters()
    {
        return Arrays::concat(Arrays::map($this->conditions, function ($where) {
            return $where->getParameters();
        }));
    }
}