<?php
namespace Ouzo\Db\Dialect;

use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Utilities\Joiner;

abstract class Dialect
{
    /**
     * @var Query
     */
    protected $_query;

    public function select()
    {
        if ($this->_query->type == QueryType::$SELECT) {
            return 'SELECT ' . (empty($this->_query->selectColumns) ? '*' : Joiner::on(', ')->map(DialectUtil::_addAliases())->join($this->_query->selectColumns));
        }
        if ($this->_query->type == QueryType::$COUNT) {
            return 'SELECT count(*)';
        }
        return '';
    }

    public function update()
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->_query->updateAttributes);
        return "UPDATE {$this->_query->table} set $attributes";
    }

    public function insert()
    {
        $data = $this->_query->updateAttributes;

        $columns = array_keys($data);
        $values = array_values($data);

        $joinedColumns = implode(', ', $columns);
        $joinedValues = implode(', ', array_fill(0, count($values), '?'));

        return "INSERT INTO {$this->_query->table} ($joinedColumns) VALUES ($joinedValues)";
    }

    public function delete()
    {
        return "DELETE";
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

    public function groupBy()
    {
        $groupBy = $this->_query->groupBy;
        if ($groupBy) {
            return ' GROUP BY ' .  (is_array($groupBy) ? implode(', ', $groupBy) : $groupBy);
        }
        return '';
    }

    public function order()
    {
        $order = $this->_query->order;
        if ($order) {
            return ' ORDER BY ' . (is_array($order) ? implode(', ', $order) : $order);
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

    public function from()
    {
        $alias = $this->_query->aliasTable ? ' AS ' . $this->_query->aliasTable : '';
        return ' FROM ' . $this->_query->table . $alias;
    }

    public function buildQuery(Query $query)
    {
        $this->_query = $query;
        $sql = '';

        if ($query->type == QueryType::$UPDATE) {
            $sql .= $this->update();
            $sql .= $this->where();

        } else if ($query->type == QueryType::$INSERT) {
            $sql .= $this->insert();

        } else if ($query->type == QueryType::$DELETE) {
            $sql .= $this->delete();
            $sql .= $this->from();
            $sql .= $this->join();
            $sql .= $this->where();

        } else if ($query->type == QueryType::$COUNT) {
            $sql .= $this->select();
            $sql .= $this->from();
            $sql .= $this->join();
            $sql .= $this->where();

        } else {
            $sql .= $this->select();
            $sql .= $this->from();
            $sql .= $this->join();
            $sql .= $this->where();
            $sql .= $this->groupBy();
            $sql .= $this->order();
            $sql .= $this->limit();
            $sql .= $this->offset();
        }
        return rtrim($sql);
    }

    public function getExceptionForError($errorInfo)
    {
        if ($this->isConnectionError($errorInfo)) {
            return '\Ouzo\DbConnectionException';
        }
        return '\Ouzo\DbException';
    }

    public function isConnectionError($errorInfo)
    {
        return in_array($this->getErrorCode($errorInfo), $this->getConnectionErrorCodes());
    }

    abstract public function getConnectionErrorCodes();

    abstract public function getErrorCode($errorInfo);
}