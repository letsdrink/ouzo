<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Restrictions;
use Ouzo\Utilities\Arrays;

class WhereClause
{
    public $where;
    public $values;

    public function __construct($where, $whereValues = array())
    {
        $this->where = $where;
        $this->values = $this->prepare($where, $whereValues);
    }

    private function prepare($where, $whereValues)
    {
        if (is_array($where)) {
            foreach ($where as $column => $value) {
                if ($value === null) {
                    $this->where[$column] = Restrictions::isNull();
                }
            }
            return Arrays::flatten(array_values($this->where));
        }
        return $whereValues;
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

    public function methodJoined()
    {
        return ' AND ';
    }
}
