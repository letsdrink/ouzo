<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
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
use PDOStatement;

class QueryExecutor
{
    /** @var Db */
    private $db;
    /** @var Dialect */
    private $adapter;
    /** @var Query */
    private $query;
    /** @var array */
    private $boundValues = [];

    public $sql;
    public $fetchStyle = PDO::FETCH_ASSOC;

    /**
     * @param Db $db
     * @param Query $query
     */
    public function __construct(Db $db, Query $query)
    {
        $this->db = $db;
        $this->query = $query;

        $this->adapter = DialectFactory::create();
    }

    /**
     * @param Db $db
     * @param Query $query
     * @throws InvalidArgumentException
     * @return QueryExecutor|EmptyQueryExecutor
     */
    public static function prepare($db, $query)
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

    /**
     * @return mixed
     */
    public function fetch()
    {
        $this->buildQuery();
        return $this->_fetch('fetch');
    }

    /**
     * @return mixed
     */
    public function fetchAll()
    {
        $this->buildQuery();
        return $this->_fetch('fetchAll');
    }

    /**
     * @return StatementIterator
     */
    public function fetchIterator()
    {
        $this->buildQuery();
        $statement = StatementExecutor::prepare($this->db->_dbHandle, $this->sql, $this->boundValues, $this->query->options);
        return $statement->fetchIterator();

    }

    /**
     * @return int
     */
    public function execute()
    {
        $this->buildQuery();
        return $this->db->execute($this->sql, $this->boundValues);
    }

    /**
     * @param string $sequence
     * @return int|null
     */
    public function insert($sequence = '')
    {
        $this->execute();
        return $sequence ? (int)$this->db->lastInsertId($sequence) : null;
    }

    /**
     * @param string $function
     * @return mixed
     */
    private function _fetch($function)
    {
        $statement = StatementExecutor::prepare($this->db->_dbHandle, $this->sql, $this->boundValues, $this->query->options);
        return $statement->executeAndFetch($function, $this->fetchStyle);
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getBoundValues()
    {
        return $this->boundValues;
    }

    /**
     * @return string
     */
    public function lastErrorMessage()
    {
        return $this->db->lastErrorMessage();
    }

    /**
     * @return void
     */
    public function buildQuery()
    {
        $this->fetchStyle = $this->query->selectType;
        $queryBindValuesExtractor = new QueryBoundValuesExtractor($this->query);
        $this->boundValues = $queryBindValuesExtractor->extract();
        $this->sql = $this->adapter->buildQuery($this->query);
    }

    /**
     * @param Query $query
     * @return bool
     */
    private static function isEmptyResult($query)
    {
        return $query->limit === 0 || self::whereClauseNeverSatisfied($query->whereClauses);
    }

    /**
     * @param WhereClause $whereClauses
     * @return bool
     */
    private static function whereClauseNeverSatisfied($whereClauses)
    {
        return Arrays::any($whereClauses, function (WhereClause $whereClause) {
            return $whereClause->isNeverSatisfied();
        });
    }
}
