<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Db\QueryType;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Joiner;

class PostgresDialect implements Dialect
{
    private $_query;

    public function buildQuery($query)
    {
        $this->_query = $query;

        $sql = DialectUtil::buildQueryPrefix($query->type);
        $sql .= $this->select();
        $sql .= $this->from();
        $sql .= $this->join();
        $sql .= $this->where();
        $sql .= $this->order();
        $sql .= $this->limit();
        $sql .= $this->offset();

        return rtrim($sql);
    }

    public function select()
    {
        if ($this->_query->type == QueryType::$SELECT) {
            $sql = ' ' . (empty($this->_query->selectColumns) ? 'main.*' : Joiner::on(', ')->map(DialectUtil::_addAliases())->join($this->_query->selectColumns));
        } else if ($this->_query->type == QueryType::$COUNT) {
            $sql = ' count(*)';
        } else {
            $sql = '';
        }
        return $sql;
    }

    public function from()
    {
        return ' FROM ' . $this->_query->table . ' AS main';
    }

    public function join()
    {
        if (!empty($this->_query->joinTable)) {
            return ' LEFT JOIN ' . $this->_query->joinTable . ' AS joined ON joined.' . $this->_query->joinKey . ' = main.' . $this->_query->idName;
        }
        return '';
    }

    public function where()
    {
        $where = DialectUtil::buildWhereQuery($this->_query->whereClauses);
        if ($where) {
            return ' WHERE ' . $where;
        }
        return '';
    }

    public function order()
    {
        if ($this->_query->order) {
            return ' ORDER BY ' . (is_array($this->_query->order) ? implode(', ', $this->_query->order) : $this->_query->order);
        }
        return '';
    }

    public function limit()
    {
        if ($this->_query->limit) {
            return ' LIMIT ?';
        }
        return '';
    }

    public function offset()
    {
        if ($this->_query->offset) {
            return ' OFFSET ?';
        }
        return '';
    }
}