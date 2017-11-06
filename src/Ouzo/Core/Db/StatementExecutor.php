<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Logger\Logger;
use Ouzo\Utilities\Objects;
use PDO;
use PDOStatement;

class StatementExecutor
{
    /** @var string */
    private $sql;
    /** @var string */
    private $humanizedSql;
    /** @var PDO */
    private $dbHandle;
    /** @var array */
    private $boundValues;
    /** @var PDOExecutor */
    private $pdoExecutor;

    /**
     * @param PDO $dbHandle
     * @param string $sql
     * @param array $boundValues
     * @param PDOExecutor $pdoExecutor
     */
    private function __construct($dbHandle, $sql, $boundValues, PDOExecutor $pdoExecutor)
    {
        $this->boundValues = $boundValues;
        $this->dbHandle = $dbHandle;
        $this->sql = $sql;
        $this->humanizedSql = QueryHumanizer::humanize($sql);
        $this->pdoExecutor = $pdoExecutor;
    }

    /**
     * @param \Closure $afterCallback
     * @return mixed
     */
    private function _execute($afterCallback)
    {
        return Stats::trace($this->humanizedSql, $this->boundValues, function () use ($afterCallback) {
            return $this->internalExecute($afterCallback);
        });
    }

    /**
     * @param \Closure $afterCallback
     * @return mixed
     */
    private function internalExecute($afterCallback)
    {
        $pdoStatement = $this->_createPdoStatement();
        $result = call_user_func($afterCallback, $pdoStatement);
        $pdoStatement->closeCursor();
        return $result;
    }

    /**
     * Returns number of affected rows
     * @return mixed
     */
    public function execute()
    {
        return $this->_execute(function (PDOStatement $pdoStatement) {
            return $pdoStatement->rowCount();
        });
    }

    /**
     * @param string $function
     * @param string $fetchStyle
     * @return mixed
     */
    public function executeAndFetch($function, $fetchStyle)
    {
        return $this->_execute(function ($pdoStatement) use ($function, $fetchStyle) {
            return $pdoStatement->$function($fetchStyle);
        });
    }

    /**
     * @param int $fetchMode
     * @return mixed
     */
    public function fetch($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->executeAndFetch('fetch', $fetchMode);
    }

    /**
     * @param int $fetchMode
     * @return mixed
     */
    public function fetchAll($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->executeAndFetch('fetchAll', $fetchMode);
    }

    /**
     * @param PDO $dbHandle
     * @param string $sql
     * @param array $boundValues
     * @param array $options
     * @return StatementExecutor
     */
    public static function prepare($dbHandle, $sql, $boundValues, $options)
    {
        $pdoExecutor = PDOExecutor::newInstance($options);
        return new StatementExecutor($dbHandle, $sql, $boundValues, $pdoExecutor);
    }

    /**
     * @return StatementIterator
     */
    public function fetchIterator()
    {
        return Stats::trace($this->humanizedSql, $this->boundValues, function () {
            $pdoStatement = $this->_createPdoStatement([PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL]);
            return new StatementIterator($pdoStatement);
        });
    }

    /**
     * @param array $options
     * @return PDOStatement
     */
    public function _createPdoStatement($options = [])
    {
        $sqlString = $this->humanizedSql . ' with params: ' . Objects::toString($this->boundValues);
        Logger::getLogger(__CLASS__)->info("Query: %s", [$sqlString]);

        return $this->pdoExecutor->createPDOStatement($this->dbHandle, $this->sql, $this->boundValues, $sqlString, $options);
    }
}
