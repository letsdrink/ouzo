<?php


namespace Ouzo\Tools\Model\Template\Dialect;


class PostgresDialect extends Dialect
{

    public $_tableName;

    function __construct($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function primaryKey()
    {
    }

    public function sequence()
    {
    }

    public function _tableName()
    {
    }

    public function columns()
    {
    }

    public function columnType($columnName)
    {
    }
}