<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class UsingClause
{
    /** @var string */
    public $table;
    /** @var string */
    public $alias;

    /**
     * @param string $table
     * @param string $alias
     */
    public function __construct($table, $alias)
    {
        $this->table = $table;
        $this->alias = $alias;
    }
}
