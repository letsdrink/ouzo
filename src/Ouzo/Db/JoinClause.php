<?php
namespace Ouzo\Db;

class JoinClause
{
    public $joinTable;
    public $alias;
    public $joinColumn;
    public $joinedColumn;
    private $_joinedColumnTable;
    public $type;
    public $onClause;

    function __construct($joinTable, $joinColumn, $joinedColumn, $joinedColumnTable, $alias = null, $type = 'LEFT', $onClause)
    {
        $this->joinTable = $joinTable;
        $this->joinColumn = $joinColumn;
        $this->joinedColumn = $joinedColumn;
        $this->_joinedColumnTable = $joinedColumnTable;
        $this->alias = $alias;
        $this->type = $type;
        $this->onClause = $onClause;
    }

    public function getJoinedColumnWithTable()
    {
        return $this->_joinedColumnTable . '.' . $this->joinedColumn;
    }

    public function getJoinColumnWithTable()
    {
        $table = $this->alias? : $this->joinTable;
        return $table . '.' . $this->joinColumn;
    }
}