<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

use InvalidArgumentException;
use Ouzo\Db\ModelQueryBuilder;

abstract class WhereClause
{
    abstract public function isEmpty();

    abstract public function toSql();

    abstract public function getParameters();

    public function isNeverSatisfied()
    {
        return false;
    }

    public static function isNotEmptyFunction()
    {
        return function (WhereClause $whereClause) {
            return !$whereClause->isEmpty();
        };
    }

    public static function create($where, $parameters = [])
    {
        if (is_null($where)) {
            return new EmptyWhereClause();
        }
        if ($where instanceof WhereClause) {
            return $where;
        }
        if (is_array($where)) {
            return new ArrayWhereClause($where, 'AND');
        }
        if (is_string($where)) {
            return new SqlWhereClause($where, $parameters);
        }
        throw new InvalidArgumentException("Cannot create a WhereClause for given arguments");
    }

    public static function exists(ModelQueryBuilder $queryBuilder)
    {
        return new ExistsClause($queryBuilder->getQuery());
    }

    public static function notExists(ModelQueryBuilder $queryBuilder)
    {
        return new ExistsClause($queryBuilder->getQuery(), true);
    }
}
