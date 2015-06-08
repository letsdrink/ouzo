<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class JoinClause
{
    public $joinTable;
    public $alias;
    public $joinColumn;
    public $joinedColumn;
    public $type;
    public $onClauses;

    private $_joinedColumnTable;

    public function __construct($joinTable, $joinColumn, $joinedColumn, $joinedColumnTable, $alias = null, $type = 'LEFT', $onClauses)
    {
        $this->joinTable = $joinTable;
        $this->joinColumn = $joinColumn;
        $this->joinedColumn = $joinedColumn;
        $this->_joinedColumnTable = $joinedColumnTable;
        $this->alias = $alias;
        $this->type = $type;
        $this->onClauses = $onClauses;
    }

    public function getJoinedColumnWithTable()
    {
        return $this->_joinedColumnTable . '.' . $this->joinedColumn;
    }

    public function getJoinColumnWithTable()
    {
        $table = $this->alias ?: $this->joinTable;
        return $table . '.' . $this->joinColumn;
    }
}
