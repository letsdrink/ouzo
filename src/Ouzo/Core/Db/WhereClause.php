<?php
namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

class WhereClause
{
    public $where;
    public $values;

    public function __construct($where, $whereValues = array())
    {
        $this->where = $where;
        $this->values = is_array($where) ? Arrays::flatten(array_values($where)) : $whereValues;
    }

    public function isEmpty()
    {
        return empty($this->where);
    }

    public function isNeverSatisfied()
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

    public static function isNotEmptyFunction()
    {
        return function ($whereClause) {
            return !$whereClause->isEmpty();
        };
    }
}
