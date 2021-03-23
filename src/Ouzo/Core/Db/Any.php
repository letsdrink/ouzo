<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db\WhereClause\ArrayWhereClause;
use Ouzo\Db\WhereClause\OrClause;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Utilities\Arrays;

class Any
{
    public static function of(array $conditions): WhereClause
    {
        if (Arrays::isAssociative($conditions)) {
            return new ArrayWhereClause($conditions, 'OR');
        }
        return new OrClause($conditions);
    }
}
