<?php

namespace Thulium\Db;

class WhereClause
{
    public $where;
    public $values;

    function __construct($where, $whereValues)
    {
        $this->where = $where;
        $this->values = $whereValues;
    }

}