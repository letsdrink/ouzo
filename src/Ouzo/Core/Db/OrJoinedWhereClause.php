<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class OrJoinedWhereClause extends WhereClause
{
    public function methodJoined()
    {
        return ' OR ';
    }
}
