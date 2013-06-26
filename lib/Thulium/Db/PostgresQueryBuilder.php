<?php
namespace Thulium\Db;

use InvalidArgumentException;
use PDO;
use Thulium\Db;
use Thulium\Logger;
use Thulium\Utilities\Arrays;
use Thulium\Utilities\FluentArray;
use Thulium\Utilities\Joiner;
use Thulium\Utilities\Objects;

class PostgresQueryBuilder extends DbQueryBuilder
{
    private $_delete;

    public function __construct(Db $dbHandle, array $columns = array(), $delete = false)
    {
        parent::__construct($dbHandle);

        $this->_delete = $delete;
        $this->_query = $delete ? 'DELETE ' : 'SELECT ';

        $this->_columns($columns);
    }

    private function _columns(array $columns = array())
    {
        if (!$this->_delete) {
            if (!empty($columns)) {
                $this->_fetchStyle = PDO::FETCH_NUM;
                $this->_query .= Joiner::on(', ')->map($this->addAliases())->join($columns);
            } else {
                $this->_query .= 'main.*';
            }
        }
        return $this;
    }

    public function from($table = null)
    {
        if (empty($table)) {
            throw new InvalidArgumentException("$table cannot be empty");
        }
        $this->_query .= ' FROM ' . $table . ' AS main ';
        return $this;
    }

    public function where($where = '', $values)
    {
        if ($this->isEmptyResult($where)) {
            return new EmptyQueryBuilder();
        }

        list ($where, $values) = $this->_buildWhereQuery($where, $values);

        if (!empty($where)) {
            $this->_query .= ' WHERE ' . (stripos($where, 'OR') ? '(' . $where . ')' : $where);
            $this->_addBindValue($values);
        }
        return $this;
    }

    public function order($value)
    {
        if ($value) {
            $this->_query .= ' ORDER BY ' . (is_array($value) ? implode(', ', $value) : $value);
        }
        return $this;
    }

    public function offset($value)
    {
        if ($value) {
            $this->_query .= " OFFSET ? ";
            $this->_addBindValue($value);
        }
        return $this;
    }

    public function limit($value)
    {
        if ($value) {
            $this->_query .= " LIMIT ? ";
            $this->_addBindValue($value);
        }
        return $this;
    }

    public function join($joinTable, $joinKey, $idName)
    {
        if (!empty($joinTable)) {
            $this->_query .= ' LEFT JOIN ' . $joinTable . ' AS joined ON joined.' . $joinKey . ' = main.' . $idName;
        }
        return $this;
    }

    private function addAliases()
    {
        return function ($alias, $column) {
            return $column . (is_string($alias) ? ' AS ' . $alias : '');
        };
    }

    private function _buildWhereQuery($where, $values)
    {
        return is_array($where) ? $this->_whereArray($where) : array($where, $values);
    }

    private function _whereArray($params)
    {
        $where = implode(' AND ', $this->_buildWhereKeys($params));
        $values = Arrays::flatten(array_values($params));
        return array($where, $values);
    }

    private function _buildWhereKeys($params)
    {
        $keys = array();
        foreach ($params as $key => $value) {
            $keys[] = $this->_buildWhereKey($value, $key);
        }
        return $keys;
    }

    private function _buildWhereKey($value, $key)
    {
        if (is_array($value)) {
            $in = implode(', ', array_fill(0, count($value), '?'));
            return $key . ' IN (' . $in . ')';
        }
        return $key . ' = ?';
    }

    private function isEmptyResult($where)
    {
        if (is_array($where)) {
            foreach ($where as $value) {
                if (is_array($value) && sizeof($value) == 0) {
                    return true;
                }
            }
        }
        return false;
    }
}
