<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

class EmptyWhereClause extends WhereClause
{

    public function isEmpty()
    {
        return true;
    }

    public function toSql()
    {
        return '';
    }

    public function getParameters()
    {
        return array();
    }
}