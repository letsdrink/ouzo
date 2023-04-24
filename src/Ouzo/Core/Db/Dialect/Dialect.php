<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\Dialect;

use Ouzo\Db\JoinClause;
use Ouzo\Db\OnConflict;
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\DbConnectionException;
use Ouzo\DbException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Strings;
use PDO;

abstract class Dialect
{
    protected Query $query;

    public function select(): string
    {
        if ($this->query->type == QueryType::$SELECT) {
            $distinct = $this->query->distinct ? 'DISTINCT ' : (empty($this->query->distinctOnColumns) ? Strings::EMPTY_STRING : $this->getDistinctOnQuery());
            $columns = empty($this->query->selectColumns) ? '*' : Joiner::on(', ')->map(DialectUtil::addAliases())->join($this->query->selectColumns);
            return "SELECT {$distinct}{$columns}";
        }
        if ($this->query->type == QueryType::$COUNT) {
            return 'SELECT count(*)';
        }
        return Strings::EMPTY_STRING;
    }

    public function update(): string
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        $table = $this->table();
        return "UPDATE {$table} SET {$attributes}";
    }

    public function insert(): string
    {
        $data = $this->query->updateAttributes;

        $columns = array_keys($data);
        $values = array_values($data);

        if ($values) {
            $joinedColumns = implode(', ', $columns);
            $joinedValues = implode(', ', array_fill(0, count($values), '?'));
            return "INSERT INTO {$this->query->table} ({$joinedColumns}) VALUES ({$joinedValues})";
        } else {
            return $this->insertEmptyRow();
        }
    }

    abstract public function onConflictUpdate(): string;

    abstract public function onConflictDoNothing(): string;

    public function delete(): string
    {
        return "DELETE";
    }

    public function join(): string
    {
        $join = DialectUtil::buildJoinQuery($this->query->joinClauses);
        if ($join) {
            return " {$join}";
        }
        return '';
    }

    public function where(): string
    {
        return $this->whereClause(Arrays::toArray($this->query->whereClauses));
    }

    private function whereWithUsing(): string
    {
        $usingClauses = $this->query->usingClauses;
        $whereClauses = Arrays::map($usingClauses, function (JoinClause $usingClause) {
            return WhereClause::create($usingClause->getJoinColumnWithTable() . ' = ' . $usingClause->getJoinedColumnWithTable());
        });
        return $this->whereClause(array_merge($whereClauses, $this->query->whereClauses));
    }

    public function using(): string
    {
        return $this->usingClause($this->query->usingClauses);
    }

    public function groupBy(): string
    {
        $groupBy = $this->query->groupBy;
        if ($groupBy) {
            $groupBy = is_array($groupBy) ? implode(', ', $groupBy) : $groupBy;
            return " GROUP BY {$groupBy}";
        }
        return '';
    }

    public function order(): string
    {
        $order = $this->query->order;
        if ($order) {
            $order = is_array($order) ? implode(', ', $order) : $order;
            return " ORDER BY {$order}";
        }
        return '';
    }

    public function limit(): string
    {
        if ($this->query->limit !== null) {
            return ' LIMIT ?';
        }
        return '';
    }

    public function offset(): string
    {
        if ($this->query->offset) {
            return ' OFFSET ?';
        }
        return '';
    }

    public function table(): string
    {
        $alias = $this->query->aliasTable ? " AS {$this->query->aliasTable}" : '';
        return $this->tableOrSubQuery() . $alias;
    }

    public function tableOrSubQuery(): string
    {
        if ($this->query->table instanceof Query) {
            $query = DialectFactory::create()->buildQuery($this->query->table);
            return "({$query})";
        }
        return $this->query->table;
    }

    public function from(): string
    {
        return " FROM {$this->table()}";
    }

    public function comment(): string
    {
        return $this->query->comment ? " /* {$this->query->comment} */ " : '';
    }

    public function lockForUpdate(): string
    {
        return $this->query->lockForUpdate ? ' FOR UPDATE' : '';
    }

    public function buildQuery(Query $query): string
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

    public function getExceptionForError(array $errorInfo): string
    {
        if ($this->isConnectionError($errorInfo)) {
            return DbConnectionException::class;
        }
        return DbException::class;
    }

    public function isConnectionError(array $errorInfo): bool
    {
        return in_array($this->getErrorCode($errorInfo), $this->getConnectionErrorCodes());
    }

    abstract public function getConnectionErrorCodes(): array;

    abstract public function getErrorCode(array $errorInfo): mixed;

    /** @param string[] $columns */
    abstract public function batchInsert(
        string $table, string $primaryKey, array $columns, int $batchSize,
        ?OnConflict $onConflict
    ): string;

    abstract public function regexpMatcher(): string;

    /** @param WhereClause[] $whereClauses */
    protected function whereClause(array $whereClauses): string
    {
        $where = DialectUtil::buildWhereQuery($whereClauses);
        if ($where) {
            return " WHERE {$where}";
        }
        return '';
    }

    /** @param JoinClause[] $usingClauses */
    protected function usingClause(array $usingClauses, string $glue = ', ', string $table = null, string $alias = null): string
    {
        $using = DialectUtil::buildUsingQuery($usingClauses, $glue, $table, $alias);
        if ($using) {
            return " USING {$using}";
        }
        return '';
    }

    abstract protected function insertEmptyRow(): string;

    /** @return int[] */
    public function getIteratorOptions(): array
    {
        return [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL];
    }

    abstract protected function quote(string $word): string;

    abstract protected function getDistinctOnQuery(): string;
}
