<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

use InvalidArgumentException;
use Ouzo\Db\Any;

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
        return function ($whereClause) {
            return !$whereClause->isEmpty();
        };
    }

    public static function create($where, $parameters = array())
    {
        if ($where instanceof WhereClause) {
            return $where;
        }
        if (is_array($where)) {
            return new ArrayWhereClause($where, 'AND');
        }
        if (is_string($where)) {
            return new SqlWhereClause($where, $parameters);
        }
        if ($where instanceof Any) {
            return new ArrayWhereClause($where->getConditions(), 'OR');
        }
        throw new InvalidArgumentException("Cannot create a WhereClause for given arguments");
    }
}
