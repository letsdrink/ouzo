<?php

namespace Ouzo\Db;

use Ouzo\DbException;
use Ouzo\Logger\Logger;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use PDO;
use PDOStatement;

class StatementExecutor
{
    private $_sql;
    private $_humanizedSql;
    private $_dbHandle;
    private $_boundValues;
    /**
     * @var PDOStatement
     */
    private $_pdoStatement;

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

    public function getBoundValues()
    {
        return $this->_boundValues;
    }

    public function getPdoStatement()
    {
        return $this->_pdoStatement;
    }

    private function _execute($afterCallback)
    {
        $obj = $this;
        return Stats::trace($this->_humanizedSql, $this->getBoundValues(), function () use ($obj, $afterCallback) {
            return $obj->_internalExecute($afterCallback);
        });
    }

    function _internalExecute($afterCallback)
    {
        Logger::getLogger(__CLASS__)->info("Query: %s Params: %s", array($this->_humanizedSql, Objects::toString($this->getBoundValues())));

        $this->_prepareAndBind();

        return call_user_func($afterCallback);
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
        $obj = $this;
        return $this->_execute(function () use ($obj) {
            return $obj->getPdoStatement()->rowCount();
        });
    }

    public function executeAndFetch($function, $fetchStyle)
    {
        $obj = $this;
        return $this->_execute(function () use ($obj, $function, $fetchStyle) {
            return $obj->getPdoStatement()->$function($fetchStyle);
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

    public function _prepareAndBind()
    {
        $queryString = $this->_createQuerySql($this->_humanizedSql);
        $this->_pdoStatement = $this->_dbHandle->prepare($this->_sql);

        if (!$this->_pdoStatement) {
            throw new DbException('Exception: query: ' . $queryString . ' failed: ' . $this->lastDbErrorMessage());
        }

        foreach ($this->_boundValues as $key => $valueBind) {
            $type = ParameterType::getType($valueBind);
            $this->_pdoStatement->bindValue($key + 1, $valueBind, $type);
        }

        if (!$this->_pdoStatement->execute()) {
            throw PDOExceptionExtractor::getException($this->_pdoStatement, $queryString);
        }
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