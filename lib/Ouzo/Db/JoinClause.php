<?php
namespace Ouzo\Db;

class JoinClause
{
    public $joinTable;
    public $joinColumn;
    public $joinedColumn;
    private $_joinedColumnTable;

    function __construct($joinTable, $joinColumn, $joinedColumn, $joinedColumnTable)
    {
        $this->joinTable = $joinTable;
        $this->joinColumn = $joinColumn;
        $this->joinedColumn = $joinedColumn;
        $this->_joinedColumnTable = $joinedColumnTable;
    }

    public function getJoinedColumnWithTable()
    {
        $table = $this->_joinedColumnTable;
        return $table . '.' . $this->joinedColumn;
    }

    public function getJoinColumnWithTable()
    {
        $table = $this->joinTable;
        return $table . '.' . $this->joinColumn;
    }
}