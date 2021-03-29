<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\Db;
use Ouzo\Db\Dialect\Dialect;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Utilities\Arrays;
use PDO;

class QueryExecutor
{
    private Dialect $adapter;
    private array $boundValues = [];

    public string $sql;
    public int $fetchStyle = PDO::FETCH_ASSOC;

    public function __construct(private Db $db, private Query $query)
    {
        $this->adapter = DialectFactory::create();
    }

    public static function prepare(?Db $db, ?Query $query): EmptyQueryExecutor|QueryExecutor
    {
        if (empty($db) || !$db instanceof Db) {
            throw new InvalidArgumentException("Database handler not provided or is of wrong type");
        }
        if (!$query) {
            throw new InvalidArgumentException("Query object not provided");
        }
        if (!$query->table) {
            throw new InvalidArgumentException("Table name cannot be empty");
        }

        if (self::isEmptyResult($query)) {
            return new EmptyQueryExecutor();
        }
        return new QueryExecutor($db, $query);
    }

    public function fetch(): mixed
    {
        $this->buildQuery();
        return $this->prepareAndFetch('fetch');
    }

    public function fetchAll(): mixed
    {
        $this->buildQuery();
        return $this->prepareAndFetch('fetchAll');
    }

    public function fetchIterator(): StatementIterator
    {
        $this->buildQuery();
        $statement = StatementExecutor::prepare($this->db->dbHandle, $this->sql, $this->boundValues, $this->query->options);
        return $statement->fetchIterator($this->adapter->getIteratorOptions());
    }

    public function execute(): int
    {
        $this->buildQuery();
        return $this->db->execute($this->sql, $this->boundValues);
    }

    public function insert(?string $sequence = ''): string|int|null
    {
        $this->execute();
        return $sequence ? (int)$this->db->lastInsertId($sequence) : null;
    }

    private function prepareAndFetch(string $function): mixed
    {
        $statement = StatementExecutor::prepare($this->db->dbHandle, $this->sql, $this->boundValues, $this->query->options);
        return $statement->executeAndFetch($function, $this->fetchStyle);
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getBoundValues(): array
    {
        return $this->boundValues;
    }

    public function lastErrorMessage(): string
    {
        return $this->db->lastErrorMessage();
    }

    public function buildQuery(): void
    {
        $this->fetchStyle = $this->query->selectType;
        $queryBindValuesExtractor = new QueryBoundValuesExtractor($this->query);
        $this->boundValues = $queryBindValuesExtractor->extract();
        $this->sql = $this->adapter->buildQuery($this->query);
    }

    private static function isEmptyResult(Query $query): bool
    {
        return $query->limit === 0 || self::whereClauseNeverSatisfied($query->whereClauses);
    }

    /** @param WhereClause[] $whereClauses */
    private static function whereClauseNeverSatisfied(array $whereClauses): bool
    {
        return Arrays::any($whereClauses, fn(WhereClause $whereClause) => $whereClause->isNeverSatisfied());
    }
}
