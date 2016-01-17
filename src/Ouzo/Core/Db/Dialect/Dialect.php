<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use Ouzo\Db\JoinClause;
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Utilities\Arrays;
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
            return 'SELECT ' .
                ($this->_query->distinct ? 'DISTINCT ' : '') .
                (empty($this->_query->selectColumns) ? '*' : Joiner::on(', ')->map(DialectUtil::_addAliases())->join($this->_query->selectColumns));
        }
        if ($this->_query->type == QueryType::$COUNT) {
            return 'SELECT count(*)';
        }
        return '';
    }

    public function update()
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->_query->updateAttributes);
        $table = $this->table();
        return "UPDATE $table SET $attributes";
    }

    public function insert()
    {
        $data = $this->_query->updateAttributes;

        $columns = array_keys($data);
        $values = array_values($data);

        if ($values) {
            $joinedColumns = implode(', ', $columns);
            $joinedValues = implode(', ', array_fill(0, count($values), '?'));
            return "INSERT INTO {$this->_query->table} ($joinedColumns) VALUES ($joinedValues)";
        } else {
            return $this->insertEmptyRow();
        }
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
        return $this->_where($this->_query->whereClauses);
    }

    private function whereWithUsing()
    {
        $usingClauses = $this->_query->usingClauses;
        $whereClauses = Arrays::map($usingClauses, function (JoinClause $usingClause) {
            return WhereClause::create($usingClause->getJoinColumnWithTable() . ' = ' . $usingClause->getJoinedColumnWithTable());
        });
        return $this->_where(array_merge($whereClauses, $this->_query->whereClauses));
    }

    public function using()
    {
        return $this->_using($this->_query->usingClauses);
    }

    public function groupBy()
    {
        $groupBy = $this->_query->groupBy;
        if ($groupBy) {
            return ' GROUP BY ' . (is_array($groupBy) ? implode(', ', $groupBy) : $groupBy);
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

    public function table()
    {
        $alias = $this->_query->aliasTable ? ' AS ' . $this->_query->aliasTable : '';
        return $this->tableOrSubQuery() . $alias;
    }

    public function tableOrSubQuery()
    {
        if ($this->_query->table instanceof Query) {
            return '(' . DialectFactory::create()->buildQuery($this->_query->table) . ')';
        }
        return $this->_query->table;
    }

    public function from()
    {
        return ' FROM ' . $this->table();
    }

    public function lockForUpdate()
    {
        return $this->_query->lockForUpdate ? ' FOR UPDATE' : '';
    }

    public function buildQuery(Query $query)
    {
        $this->_query = $query;
        $sql = '';

        if ($query->type == QueryType::$UPDATE) {
            $sql .= $this->update();
            $sql .= $this->where();
        } elseif ($query->type == QueryType::$INSERT) {
            $sql .= $this->insert();
        } elseif ($query->type == QueryType::$DELETE) {
            $sql .= $this->delete();
            $sql .= $this->from();
            $sql .= $this->using();
            $sql .= $this->whereWithUsing();
        } elseif ($query->type == QueryType::$COUNT) {
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
            $sql .= $this->lockForUpdate();
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

    abstract public function batchInsert($table, $primaryKey, $columns, $batchSize);

    abstract public function regexpMatcher();

    protected function _where($whereClauses)
    {
        $where = DialectUtil::buildWhereQuery($whereClauses);
        if ($where) {
            return ' WHERE ' . $where;
        }
        return '';
    }

    protected function _using($usingClauses, $glue = ', ', $table = null, $alias = null)
    {
        $using = DialectUtil::buildUsingQuery($usingClauses, $glue, $table, $alias);
        if ($using) {
            return ' USING ' . $using;
        }
        return '';
    }

    abstract protected function insertEmptyRow();
}
