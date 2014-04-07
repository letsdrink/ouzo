<?php

namespace Ouzo\Db;

use Ouzo\DbException;
use Ouzo\Logger\Logger;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use PDO;

class StatementExecutor
{
    private $_sql;
    private $_humanizedSql;
    private $_dbHandle;
    private $_boundValues;

    private function __construct($dbHandle, $sql, $boundValues, $options)
    {
        if (Arrays::getValue($options, Options::EMULATE_PREPARES)) {
            $sql = PreparedStatementEmulator::substitute($sql, $boundValues);
            $boundValues = array();
        }

        $this->_boundValues = $boundValues;
        $this->_dbHandle = $dbHandle;
        $this->_sql = $sql;
        $this->_humanizedSql = QueryHumanizer::humanize($sql);
    }

    private function _execute($afterCallback)
    {
        $obj = $this;
        return Stats::trace($this->_humanizedSql, $this->_boundValues, function () use ($obj, $afterCallback) {
            return $obj->_internalExecute($afterCallback);
        });
    }

    function _internalExecute($afterCallback)
    {
        Logger::getLogger(__CLASS__)->info("Query: %s Params: %s", array($this->_humanizedSql, Objects::toString($this->_boundValues)));

        $pdoStatement = $this->_createPDOStatement();
        $result = call_user_func($afterCallback, $pdoStatement);
        return $result;
    }


    public function _createQuerySql($humanizedSql)
    {
        return $humanizedSql . ' with params: (' . implode(', ', $this->_boundValues) . ')';
    }

    /**
     * Returns number of affected rows
     */
    public function execute()
    {
        return $this->_execute(function ($pdoStatement) {
            return $pdoStatement->rowCount();
        });
    }

    public function executeAndFetch($function, $fetchStyle)
    {
        return $this->_execute(function ($pdoStatement) use ($function, $fetchStyle) {
            return $pdoStatement->$function($fetchStyle);
        });
    }

    public function fetch($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->executeAndFetch('fetch', $fetchMode);
    }

    public function fetchAll($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->executeAndFetch('fetchAll', $fetchMode);
    }

    public function _createPDOStatement()
    {
        $queryString = $this->_createQuerySql($this->_humanizedSql);
        $pdoStatement = $this->_dbHandle->prepare($this->_sql);

        if (!$pdoStatement) {
            throw new DbException('Exception: query: ' . $queryString . ' failed: ' . $this->lastDbErrorMessage());
        }

        foreach ($this->_boundValues as $key => $valueBind) {
            $type = ParameterType::getType($valueBind);
            $pdoStatement->bindValue($key + 1, $valueBind, $type);
        }

        if (!$pdoStatement->execute()) {
            throw PDOExceptionExtractor::getException($pdoStatement, $queryString);
        }
        return $pdoStatement;
    }

    public function lastDbErrorMessage()
    {
        return PDOExceptionExtractor::errorMessageFromErrorInfo($this->_dbHandle->errorInfo());
    }

    public static function prepare($dbHandle, $sql, $boundValues, $options)
    {
        return new StatementExecutor($dbHandle, $sql, $boundValues, $options);
    }
}