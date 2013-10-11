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
        $table = $this->_joinedColumnTable ? : 'main';
        return $table . '.' . $this->joinedColumn;
    }

    public function getJoinColumnWithTable()
    {
        $table = $this->joinTable ? : 'joined';
        return $table . '.' . $this->joinColumn;
    }
}