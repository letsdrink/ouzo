<?php

namespace Thulium\Db;

use Thulium\Utilities\Arrays;

class WhereClause
{
    public $where;
    public $values;

    function __construct($where, $whereValues)
    {
        $this->where = $where;
        $this->values = is_array($where) ? Arrays::flatten(array_values($where)) : $whereValues;
    }

    public function isEmpty()
    {
        return empty($this->where);
    }

    public function isNeverTrue()
    {
        if (is_array($this->where)) {
            foreach ($this->where as $value) {
                if (is_array($value) && sizeof($value) == 0) {
                    return true;
                }
            }
        }
        return false;
    }
}