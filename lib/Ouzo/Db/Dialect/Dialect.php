<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Db\QueryType;
use Ouzo\Db\WhereClause;
use Ouzo\Utilities\FluentArray;

abstract class Dialect
{
    function _buildWhereQueryPart($whereClause)
    {
        return is_array($whereClause->where) ? implode(' AND ', $this->_buildWhereKeys($whereClause->where)) : $whereClause->where;
    }

    protected function _addAliases()
    {
        return function ($alias, $column) {
            return $column . (is_string($alias) ? ' AS ' . $alias : '');
        };
    }

    protected function _buildWhereQuery($whereClauses)
    {
        $parts = FluentArray::from($whereClauses)
            ->filter(WhereClause::isNotEmptyFunction())
            ->map(array($this, '_buildWhereQueryPart'))
            ->toArray();
        return implode(' AND ', $parts);
    }

    protected function _buildWhereKeys($params)
    {
        $keys = array();
        foreach ($params as $key => $value) {
            $keys[] = $this->_buildWhereKey($value, $key);
        }
        return $keys;
    }

    protected function _buildWhereKey($value, $key)
    {
        if (is_array($value)) {
            $in = implode(', ', array_fill(0, count($value), '?'));
            return $key . ' IN (' . $in . ')';
        }
        return $key . ' = ?';
    }

    protected function _buildQueryPrefix($type)
    {
        return $type == QueryType::$DELETE ? 'DELETE' : 'SELECT';
    }

    abstract public function select();

    abstract public function from();

    abstract public function join();

    abstract public function where();

    abstract public function order();

    abstract public function limit();

    abstract public function offset();
}