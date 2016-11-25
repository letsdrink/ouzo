<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\Db;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Utilities\Arrays;
use PDO;

class QueryExecutor
{
    /**
     * @var Db
     */
    private $_db;
    private $_adapter;
    private $_query;
    private $_boundValues = array();

    public $_sql;
    public $_fetchStyle = PDO::FETCH_ASSOC;

    public function __construct($db, $query)
    {
        $this->_db = $db;
        $this->_query = $query;

        $this->_adapter = DialectFactory::create();
    }

    /**
     * @param $db
     * @param $query
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

    public function fetch()
    {
        $this->_buildQuery();
        return $this->_fetch('fetch');
    }

    public function fetchAll()
    {
        $this->_buildQuery();
        return $this->_fetch('fetchAll');
    }

    public function fetchIterator()
    {
        $this->_buildQuery();
        $statement = StatementExecutor::prepare($this->_db->_dbHandle, $this->_sql, $this->_boundValues, $this->_query->options);
        return $statement->fetchIterator();

    }

    public function execute()
    {
        $this->_buildQuery();
        return $this->_db->execute($this->_sql, $this->_boundValues);
    }

    public function insert($sequence = '')
    {
        $this->execute();
        return $sequence ? (int)$this->_db->lastInsertId($sequence) : null;
    }

    private function _fetch($function)
    {
        $statement = StatementExecutor::prepare($this->_db->_dbHandle, $this->_sql, $this->_boundValues, $this->_query->options);
        return $statement->executeAndFetch($function, $this->_fetchStyle);
    }

    public function getSql()
    {
        return $this->_sql;
    }

    public function getBoundValues()
    {
        return $this->_boundValues;
    }

    public function lastErrorMessage()
    {
        return $this->_db->lastErrorMessage();
    }

    public function _buildQuery()
    {
        $this->_fetchStyle = $this->_query->selectType;
        $queryBindValuesExtractor = new QueryBoundValuesExtractor($this->_query);
        $this->_boundValues = $queryBindValuesExtractor->extract();
        $this->_sql = $this->_adapter->buildQuery($this->_query);
    }

    private static function isEmptyResult($query)
    {
        return $query->limit === 0 || self::whereClauseNeverSatisfied($query->whereClauses);
    }

    private static function whereClauseNeverSatisfied($whereClauses)
    {
        return Arrays::any($whereClauses, function (WhereClause $whereClause) {
            return $whereClause->isNeverSatisfied();
        });
    }
}
