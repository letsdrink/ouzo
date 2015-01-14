<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class WhereClauseFactory
{
    public static function create($where, $whereValues)
    {
        if ($where instanceof Any) {
            return new OrJoinedWhereClause($where->getConditions(), $whereValues);
        } else {
            return $where instanceof WhereClause ? $where : new WhereClause($where, $whereValues);
        }
    }
}
