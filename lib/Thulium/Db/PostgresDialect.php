<?php

namespace Thulium\Db;


use Thulium\Utilities\Joiner;

class PostgresDialect
{

    public function buildQuery($delete, $columns, $table, $joinTable, $joinKey, $idName, $order, $limit, $offset, $where)
    {
        $query = $delete ? 'DELETE ' : 'SELECT ';

        if (!$delete) {
            if (!empty($columns)) {
                $query .= Joiner::on(', ')->map($this->addAliases())->join($columns);
            } else {
                $query .= 'main.*';
            }
        }

        $query .= ' FROM ' . $table . ' AS main ';

        if (!empty($joinTable)) {
            $query .= ' LEFT JOIN ' . $joinTable . ' AS joined ON joined.' . $joinKey . ' = main.' . $idName;
        }

        $where = $this->_buildWhereQuery($where);
        if ($where) {
            $query .= ' WHERE ' . (stripos($where, 'OR') ? '(' . $where . ')' : $where);
        }

        if ($order) {
            $query .= ' ORDER BY ' . (is_array($order) ? implode(', ', $order) : $order);
        }

        if ($offset) {
            $query .= ' OFFSET ? ';
        }

        if ($limit) {
            $query .= ' LIMIT ? ';
        }

        return $query;
    }

    private function addAliases()
    {
        return function ($alias, $column) {
            return $column . (is_string($alias) ? ' AS ' . $alias : '');
        };
    }

    private function _buildWhereQuery($where)
    {
        return is_array($where) ? implode(' AND ', $this->_buildWhereKeys($where)) : $where;
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
}