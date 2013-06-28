<?php

namespace Thulium\Db;


use Thulium\Utilities\Joiner;

class PostgresDialect
{

    public function buildQuery($query)
    {
        $sql = $this->_buildQueryPrefix($query->type);

        if ($query->type == QueryType::$SELECT) {
            $sql .= ' ' . (empty($query->selectColumns) ? 'main.*' : Joiner::on(', ')->map($this->_addAliases())->join($query->selectColumns));
        } else if ($query->type == QueryType::$COUNT) {
            $sql .= ' count(*)';
        }

        $sql .= ' FROM ' . $query->table . ' AS main';

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

        return rtrim($sql);
    }

    private function _addAliases()
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

    private function _buildQueryPrefix($type)
    {
        return $type == QueryType::$DELETE ? 'DELETE' : 'SELECT';
    }
}