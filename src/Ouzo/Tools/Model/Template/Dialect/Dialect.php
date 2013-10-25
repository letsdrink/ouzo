<?php

namespace Ouzo\Tools\Model\Template\Dialect;

class Dialect
{

    public $_tableName;

    function __construct($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function primaryKey()
    {
        return '';
    }

    public function sequence()
    {
        return '';
    }

    public function tableName()
    {
        return '';
    }

    public function columns()
    {
        return array();
    }

    public function columnType($columnName)
    {
        return $columnName;
    }
}