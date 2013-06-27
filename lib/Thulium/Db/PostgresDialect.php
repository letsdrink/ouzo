<?php

namespace Thulium\Db;


use Thulium\Utilities\Joiner;

class PostgresDialect
{

    public function buildQuery($select, $query)
    {
        $sql = $select ? 'SELECT ' : 'DELETE ';

        if ($select) {
            if (!empty($query->selectColumns)) {
                if (is_array($query->selectColumns)) {
                    $sql .= Joiner::on(', ')->map($this->addAliases())->join($query->selectColumns);
                } else {
                    $sql .= $query->selectColumns;
                }
            } else {
                $sql .= 'main.*';
            }
        }

        $sql .= ' FROM ' . $query->table . ' AS main ';

        if (!empty($query->joinTable)) {
            $sql .= ' LEFT JOIN ' . $query->joinTable . ' AS joined ON joined.' . $query->joinKey . ' = main.' . $query->idName;
        }

        $where = $this->_buildWhereQuery($query->where);
        if ($where) {
            $sql .= ' WHERE ' . (stripos($where, 'OR') ? '(' . $where . ')' : $where);
        }

        if ($query->order) {
            $sql .= ' ORDER BY ' . (is_array($query->order) ? implode(', ', $query->order) : $query->order);
        }

        if ($query->limit) {
            $sql .= ' LIMIT ? ';
        }

        if ($query->offset) {
            $sql .= ' OFFSET ? ';
        }

        return $sql;
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