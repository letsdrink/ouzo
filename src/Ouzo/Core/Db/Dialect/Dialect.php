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
use Ouzo\DbConnectionException;
use Ouzo\DbException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;
use PDO;

abstract class Dialect
{
    /** @var Query */
    protected $query;

    /**
     * @return string
     */
    public function select()
    {
        if ($this->query->type == QueryType::$SELECT) {
            return 'SELECT ' .
                ($this->query->distinct ? 'DISTINCT ' : '') .
                (empty($this->query->selectColumns) ? '*' : Joiner::on(', ')->map(DialectUtil::_addAliases())->join($this->query->selectColumns));
        }
        if ($this->query->type == QueryType::$COUNT) {
            return 'SELECT count(*)';
        }
        return '';
    }

    /**
     * @return string
     */
    public function update()
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        $table = $this->table();
        return "UPDATE $table SET $attributes";
    }

    /**
     * @return string
     */
    public function insert()
    {
        $data = $this->query->updateAttributes;

        $columns = array_keys($data);
        $values = array_values($data);

        if ($values) {
            $joinedColumns = implode(', ', $columns);
            $joinedValues = implode(', ', array_fill(0, count($values), '?'));
            return "INSERT INTO {$this->query->table} ($joinedColumns) VALUES ($joinedValues)";
        } else {
            return $this->insertEmptyRow();
        }
    }

    /**
     * @return string
     */
    abstract public function onConflictUpdate();

    /**
     * @return string
     */
    abstract public function onConflictDoNothing();

    /**
     * @return string
     */
    public function delete()
    {
        return "DELETE";
    }

    /**
     * @return string
     */
    public function join()
    {
        $join = DialectUtil::buildJoinQuery($this->query->joinClauses);
        if ($join) {
            return ' ' . $join;
        }
        return '';
    }

    /**
     * @return string
     */
    public function where()
    {
        return $this->_where($this->query->whereClauses);
    }

    /**
     * @return string
     */
    private function whereWithUsing()
    {
        $usingClauses = $this->query->usingClauses;
        $whereClauses = Arrays::map($usingClauses, function (JoinClause $usingClause) {
            return WhereClause::create($usingClause->getJoinColumnWithTable() . ' = ' . $usingClause->getJoinedColumnWithTable());
        });
        return $this->_where(array_merge($whereClauses, $this->query->whereClauses));
    }

    /**
     * @return string
     */
    public function using()
    {
        return $this->_using($this->query->usingClauses);
    }

    /**
     * @return string
     */
    public function groupBy()
    {
        $groupBy = $this->query->groupBy;
        if ($groupBy) {
            return ' GROUP BY ' . (is_array($groupBy) ? implode(', ', $groupBy) : $groupBy);
        }
        return '';
    }

    /**
     * @return string
     */
    public function order()
    {
        $order = $this->query->order;
        if ($order) {
            return ' ORDER BY ' . (is_array($order) ? implode(', ', $order) : $order);
        }
        return '';
    }

    /**
     * @return string
     */
    public function limit()
    {
        if ($this->query->limit !== null) {
            return ' LIMIT ?';
        }
        return '';
    }

    /**
     * @return string
     */
    public function offset()
    {
        if ($this->query->offset) {
            return ' OFFSET ?';
        }
        return '';
    }

    /**
     * @return string
     */
    public function table()
    {
        $alias = $this->query->aliasTable ? ' AS ' . $this->query->aliasTable : '';
        return $this->tableOrSubQuery() . $alias;
    }

    /**
     * @return string
     */
    public function tableOrSubQuery()
    {
        if ($this->query->table instanceof Query) {
            return '(' . DialectFactory::create()->buildQuery($this->query->table) . ')';
        }
        return $this->query->table;
    }

    /**
     * @return string
     */
    public function from()
    {
        return ' FROM ' . $this->table();
    }

    /**
     * @return string
     */
    public function comment()
    {
        return $this->query->comment ? ' /* ' . $this->query->comment . ' */ ' : '';
    }

    /**
     * @return string
     */
    public function lockForUpdate()
    {
        return $this->query->lockForUpdate ? ' FOR UPDATE' : '';
    }

    /**
     * @param Query $query
     * @return string
     */
    public function buildQuery(Query $query)
    {
        $this->query = $query;
        $sql = '';

        if ($query->type == QueryType::$UPDATE) {
            $sql .= $this->update();
            $sql .= $this->where();
        } elseif ($query->type == QueryType::$INSERT) {
            $sql .= $this->insert();
        } elseif ($query->type == QueryType::$INSERT_OR_DO_NOTHING) {
            $sql .= $this->insert();
            $sql .= $this->onConflictDoNothing();
        } elseif ($query->type == QueryType::$UPSERT) {
            $sql .= $this->insert();
            $sql .= $this->onConflictUpdate();
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
        $sql .= $this->comment();

        return rtrim($sql);
    }

    /**
     * @param array $errorInfo
     * @return string
     */
    public function getExceptionForError($errorInfo)
    {
        if ($this->isConnectionError($errorInfo)) {
            return DbConnectionException::class;
        }
        return DbException::class;
    }

    /**
     * @param array $errorInfo
     * @return bool
     */
    public function isConnectionError($errorInfo)
    {
        return in_array($this->getErrorCode($errorInfo), $this->getConnectionErrorCodes());
    }

    /**
     * @return array
     */
    abstract public function getConnectionErrorCodes();

    /**
     * @param array $errorInfo
     * @return mixed
     */
    abstract public function getErrorCode($errorInfo);

    /**
     * @param string $table
     * @param string $primaryKey
     * @param array $columns
     * @param int $batchSize
     * @return string
     */
    abstract public function batchInsert($table, $primaryKey, $columns, $batchSize);

    /**
     * @return string
     */
    abstract public function regexpMatcher();

    /**
     * @param string $whereClauses
     * @return string
     */
    protected function _where($whereClauses)
    {
        $where = DialectUtil::buildWhereQuery($whereClauses);
        if ($where) {
            return ' WHERE ' . $where;
        }
        return '';
    }

    /**
     * @param array $usingClauses
     * @param string $glue
     * @param string $table
     * @param string $alias
     * @return string
     */
    protected function _using($usingClauses, $glue = ', ', $table = null, $alias = null)
    {
        $using = DialectUtil::buildUsingQuery($usingClauses, $glue, $table, $alias);
        if ($using) {
            return ' USING ' . $using;
        }
        return '';
    }

    /**
     * @return string
     */
    abstract protected function insertEmptyRow();

    /**
     * @return array
     */
    public function getIteratorOptions()
    {
        return [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL];
    }

    /**
     * @param string $word
     * @return string
     */
    abstract protected function quote($word);
}
