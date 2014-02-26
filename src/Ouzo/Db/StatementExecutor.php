<?php

namespace Ouzo\Db;

use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\DbException;
use Ouzo\Logger\Logger;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use PDO;

class StatementExecutor
{

    private $_sql;
    private $_dbHandle;
    private $_boundValues;
    private $_preparedQuery;

    private function __construct($dbHandle, $sql, $boundValues)
    {
        $this->_boundValues = $boundValues;
        $this->_dbHandle = $dbHandle;
        $this->_sql = $sql;
    }

    public function getBoundValues()
    {
        return $this->_boundValues;
    }

    public function getPreparedQuery()
    {
        return $this->_preparedQuery;
    }

    private function _execute($afterCallback)
    {
        $obj = $this;
        $humanizedSql = QueryHumanizer::humanize($this->_sql);
        return Stats::trace($humanizedSql, $this->getBoundValues(), function () use ($obj, $humanizedSql, $afterCallback) {
            $obj->_prepareAndBind();

            Logger::getLogger(__CLASS__)->info("Query: %s Params: %s", array($humanizedSql, Objects::toString($obj->getBoundValues())));

            $querySql = $obj->_createQuerySql($humanizedSql);
            if (!$obj->getPreparedQuery()) {
                throw new DbException('Exception: query: ' . $querySql . ' failed: ' . $obj->lastDbErrorMessage());
            }
            if (!$obj->getPreparedQuery()->execute()) {
                throw $obj->_getException($querySql);
            }
            return call_user_func($afterCallback);
        });
    }

    public function _createQuerySql($humanizedSql)
    {
        return $humanizedSql . ' with params: (' . implode(', ', $this->_boundValues) . ')';
    }

    public function _getException($querySql)
    {
        $errorInfo = $this->_preparedQuery->errorInfo();
        $exceptionClassName = DialectFactory::create()->getExceptionForError($errorInfo);
        return new $exceptionClassName(sprintf("Exception: query: %s failed: %s (%s)",
            $querySql,
            $this->_errorMessageFromErrorInfo($errorInfo),
            $this->_errorCodesFromErrorInfo($errorInfo)
        ));
    }

    /**
     * Returns number of affected rows
     */
    public function execute()
    {
        $obj = $this;
        return $this->_execute(function () use ($obj) {
            return $obj->getPreparedQuery()->rowCount();
        });
    }

    public function executeAndFetch($function, $fetchStyle)
    {
        $obj = $this;
        return $this->_execute(function () use ($obj, $function, $fetchStyle) {
            return $obj->getPreparedQuery()->$function($fetchStyle);
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
        $this->_preparedQuery = $this->_dbHandle->prepare($this->_sql);
        foreach ($this->_boundValues as $key => $valueBind) {
            $type = ParameterType::getType($valueBind);
            $this->_preparedQuery->bindValue($key + 1, $valueBind, $type);
        }
    }

    private function _errorMessageFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 2);
    }

    private function _errorCodesFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0) . " " . Arrays::getValue($errorInfo, 1);
    }

    public function lastDbErrorMessage()
    {
        return $this->_errorMessageFromErrorInfo($this->_dbHandle->errorInfo());
    }

    public static function prepare($dbHandle, $sql, $boundValues)
    {
        return new StatementExecutor($dbHandle, $sql, $boundValues);
    }
}