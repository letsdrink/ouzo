<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

use Ouzo\Restriction\Restriction;
use Ouzo\Restrictions;
use Ouzo\Utilities\Arrays;

class ArrayWhereClause extends WhereClause
{
    private $where;
    private $values;
    private $operator;

    public function __construct($where, $operator = 'AND')
    {
        foreach ($where as $column => $value) {
            if ($value === null) {
                $where[$column] = Restrictions::isNull();
            }
        }
        $this->where = $where;
        $this->values = Arrays::flatten(array_values($where));
        $this->operator = $operator;
    }

    public function isNeverSatisfied()
    {
        foreach ($this->where as $value) {
            if (is_array($value) && sizeof($value) == 0) {
                return true;
            }
        }
        return false;
    }

    public function isEmpty()
    {
        return empty($this->where);
    }

    public function toSql()
    {
        return implode(" {$this->operator} ", self::_buildWhereKeys($this->where));
    }

    public function getParameters()
    {
        return $this->values;
    }

    private static function _buildWhereKeys($params)
    {
        $keys = array();
        foreach ($params as $column => $value) {
            $keys[] = self::_buildWhereKey($column, $value);
        }
        return $keys;
    }

    private static function _buildWhereKey($column, $value)
    {
        if (is_array($value)) {
            $in = implode(', ', array_fill(0, count($value), '?'));
            return $column . ' IN (' . $in . ')';
        }
        if ($value instanceof Restriction) {
            return $value->toSql($column);
        }
        return $column . ' = ?';
    }
}
