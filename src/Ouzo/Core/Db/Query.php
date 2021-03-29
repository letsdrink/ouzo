<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\DbException;
use Ouzo\Restriction\Restriction;
use PDO;

class Query
{
    public Query|string|null $table = null;
    public ?string $aliasTable = null;
    public bool $distinct = false;
    /** @var string[] */
    public ?array $selectColumns = [];
    public int $selectType = PDO::FETCH_ASSOC;
    public string|array|null $order = null;
    public ?int $limit = null;
    public ?int $offset = null;
    public array $updateAttributes = [];
    public array $upsertConflictColumns = [];
    /** @var WhereClause[] */
    public array $whereClauses = [];
    /** @var JoinClause[] */
    public array $joinClauses = [];
    /** @var JoinClause[] */
    public array $usingClauses = [];
    public ?int $type;
    public array $options = [];
    public string|array|null $groupBy = null;
    public bool $lockForUpdate = false;
    public ?string $comment = null;

    public function __construct(?int $type = null)
    {
        $this->type = $type ? $type : QueryType::$SELECT;
    }

    public static function newInstance(?int $type = null): static
    {
        return new Query($type);
    }

    public static function insert(array $attributes): static
    {
        return Query::newInstance(QueryType::$INSERT)->attributes($attributes);
    }

    public static function insertOrDoNoting(array $attributes): static
    {
        return Query::newInstance(QueryType::$INSERT_OR_DO_NOTHING)->attributes($attributes);
    }

    public static function update(array $attributes): static
    {
        return Query::newInstance(QueryType::$UPDATE)->attributes($attributes);
    }

    public static function upsert(array $attributes): static
    {
        return Query::newInstance(QueryType::$UPSERT)->attributes($attributes);
    }

    public static function select(?array $selectColumns = null): static
    {
        $query = new Query();
        $query->selectColumns = $selectColumns;
        return $query;
    }

    public static function selectDistinct(?array $selectColumns = null): static
    {
        $query = self::select($selectColumns);
        $query->distinct = true;
        return $query;
    }

    public static function count(): static
    {
        return new Query(QueryType::$COUNT);
    }

    public static function delete(): static
    {
        return new Query(QueryType::$DELETE);
    }

    public function attributes(array $attributes): static
    {
        $this->updateAttributes = $attributes;
        return $this;
    }

    public function table(string|Query $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function into(string $table): static
    {
        return $this->table($table);
    }

    public function from(string|Query $table, ?string $alias = null): static
    {
        $this->aliasTable = $alias;
        return $this->table($table);
    }

    /** @param string|string[]|null $order */
    public function order(array|string|null $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public function where(array|string|WhereClause $where = '', mixed $whereValues = null): static
    {
        $this->validateParameters($where);
        $this->whereClauses[] = WhereClause::create($where, $whereValues);
        return $this;
    }

    public function addUsing(JoinClause $usingClause): static
    {
        $this->usingClauses[] = $usingClause;
        return $this;
    }

    public function join(string $joinTable, string $joinKey, string $idName, string|array|null $alias = null, string $type = 'LEFT', array $on = []): static
    {
        $onClauses = [WhereClause::create($on)];
        $this->joinClauses[] = new JoinClause($joinTable, $joinKey, $idName, $this->aliasTable ?: $this->table, $alias, $type, $onClauses);
        return $this;
    }

    public function addJoin(JoinClause $join): static
    {
        $this->joinClauses[] = $join;
        return $this;
    }

    public function groupBy(string $groupBy): static
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    public function lockForUpdate(): static
    {
        $this->lockForUpdate = true;
        return $this;
    }

    public function comment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function onConflict(array $upsertConflictColumns = []): static
    {
        $this->upsertConflictColumns = $upsertConflictColumns;
        return $this;
    }

    private function validateParameters(mixed $where)
    {
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                if (is_object($value) && !($value instanceof Restriction)) {
                    throw new DbException("Cannot bind object as a parameter for `{$key}`.");
                }
            }
        }
    }
}
