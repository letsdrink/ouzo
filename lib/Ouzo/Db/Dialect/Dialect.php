<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Utilities\Joiner;

class Dialect
{
    /**
     * @var Query
     */
    protected $_query;

    public function select()
    {
        if ($this->_query->type == QueryType::$SELECT) {
            $alias = $this->_query->table;
            $sql = ' ' . (empty($this->_query->selectColumns) ? $alias . '.*' : Joiner::on(', ')->map(DialectUtil::_addAliases())->join($this->_query->selectColumns));
        } else if ($this->_query->type == QueryType::$COUNT) {
            $sql = ' count(*)';
        } else {
            $sql = '';
        }
        return $sql;
    }

    public function join()
    {
        $join = DialectUtil::buildJoinQuery($this->_query->joinClauses);
        if ($join) {
            return ' ' . $join;
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