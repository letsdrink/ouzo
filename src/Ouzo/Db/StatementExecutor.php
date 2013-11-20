<?php

namespace Ouzo\Db;

use Ouzo\Config;
use Ouzo\DbException;
use Ouzo\Logger\Logger;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use PDO;

class StatementExecutor
{

    private $_sql;
    private $_dbHandle;
    public $_boundValues;
    public $_preparedQuery;

    private function __construct($dbHandle, $sql, $boundValues)
    {
        $this->_boundValues = $boundValues;
        $this->_dbHandle = $dbHandle;
        $this->_sql = $sql;
    }

    private function _execute($afterCallback)
    {
        $obj = $this;
        $humanizedSql = QueryHumanizer::humanize($this->_sql);
        return Stats::trace($humanizedSql, $this->_boundValues, function () use ($obj, $humanizedSql, $afterCallback) {
            $obj->_prepareAndBind();

            Logger::getLogger(__CLASS__)->info("Query: %s Params: %s", array($humanizedSql, Objects::toString($obj->_boundValues)));

            $querySql = $humanizedSql . ' with params: (' . implode(', ', $obj->_boundValues) . ')';

            if (!$obj->_preparedQuery) {
                throw new DbException('Exception: query: ' . $querySql . ' failed: ' . $obj->lastDbErrorMessage());
            }

            if (!$obj->_preparedQuery->execute()) {
                $dialect = Config::getValue('sql_dialect');
                $adapter = new $dialect();
                $errorInfo = $obj->_preparedQuery->errorInfo();
                $exceptionClassName = $adapter->getExceptionForError($errorInfo);
                throw new $exceptionClassName(sprintf("Exception: query: %s failed: %s (%s)",
                    $querySql,
                    $obj->_errorMessageFromErrorInfo($errorInfo),
                    $obj->_errorCodesFromErrorInfo($errorInfo)
                ));
            }
            return call_user_func($afterCallback);
        });
    }

    public function execute()
    {
        $obj = $this;
        return $this->_execute(function () use ($obj) {
            return $obj;
        });
    }

    public function executeAndFetch($function, $fetchStyle)
    {
        $obj = $this;
        return $this->_execute(function () use ($obj, $function, $fetchStyle) {
            return $obj->_preparedQuery->$function($fetchStyle);
        });
    }

    public function fetch($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->_preparedQuery->fetch($fetchMode);
    }

    public function fetchAll($fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->_preparedQuery->fetchAll($fetchMode);
    }

    public function rowCount()
    {
        return $this->_preparedQuery->rowCount();
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
        return new self($dbHandle, $sql, $boundValues);
    }
}