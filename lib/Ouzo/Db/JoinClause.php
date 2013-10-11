<?php
namespace Ouzo\Db;

class JoinClause
{
    public $joinTable;
    public $joinColumn;
    public $joinedColumn;

    function __construct($joinTable, $joinColumn, $joinedColumn)
    {
        $this->joinTable = $joinTable;
        $this->joinColumn = $joinColumn;
        $this->joinedColumn = $joinedColumn;
    }
}