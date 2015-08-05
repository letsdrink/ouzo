<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class UsingClause
{
    public $table;
    public $alias;

    public function __construct($table, $alias)
    {
        $this->table = $table;
        $this->alias = $alias;
    }
}
