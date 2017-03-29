<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;

class JoinClause
{
    /** @var string */
    public $joinTable;
    /** @var null|string */
    public $alias;
    /** @var string */
    public $joinColumn;
    /** @var string */
    public $joinedColumn;
    /** @var string */
    public $type;
    /** @var WhereClause */
    public $onClauses;

    /** @var string */
    private $joinedColumnTable;

    /**
     * @param string $joinTable
     * @param string $joinColumn
     * @param string $joinedColumn
     * @param string $joinedColumnTable
     * @param string|null $alias
     * @param string $type
     * @param WhereClause $onClauses
     */
    public function __construct($joinTable, $joinColumn, $joinedColumn, $joinedColumnTable, $alias = null, $type = 'LEFT', $onClauses)
    {
        $this->joinTable = $joinTable;
        $this->joinColumn = $joinColumn;
        $this->joinedColumn = $joinedColumn;
        $this->joinedColumnTable = $joinedColumnTable;
        $this->alias = $alias;
        $this->type = $type;
        $this->onClauses = $onClauses;
    }

    /**
     * @return string
     */
    public function getJoinedColumnWithTable()
    {
        return $this->joinedColumnTable . '.' . $this->joinedColumn;
    }

    /**
     * @return string
     */
    public function getJoinColumnWithTable()
    {
        $table = $this->alias ?: $this->joinTable;
        return $table . '.' . $this->joinColumn;
    }
}
