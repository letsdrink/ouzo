<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;

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
        return Joiner::on(' OR ')->mapValues(function ($where) {
            return $where->toSql();
        })->join($this->conditions);
    }

    public function getParameters()
    {
        return Arrays::concat(Arrays::map($this->conditions, function ($where) {
            return $where->getParameters();
        }));
    }
}