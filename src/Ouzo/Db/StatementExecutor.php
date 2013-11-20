<?php

namespace Ouzo\Db;

use Ouzo\Config;

class StatementExecutor
{

    /**
     * @var \PDOStatement $_preparedStatement
     */
    private $_preparedStatement;
    private $_querySQLWithParams;

    private function __construct($preparedStatement, $querySqlWithParams)
    {
        $this->_preparedStatement = $preparedStatement;
        $this->_querySQLWithParams = $querySqlWithParams;
    }

    private function _lastErrorMessage()
    {
        $errorInfo = $this->_preparedStatement->errorInfo();
        return $errorInfo[2];
    }

    private function _lastErrorCode()
    {
        $errorInfo = $this->_preparedStatement->errorInfo();
        return $errorInfo[1];
    }

    public function execute()
    {
        if (!$this->_preparedStatement->execute()) {
            $dialect = Config::getValue('sql_dialect');
            $adapter = new $dialect();
            $exceptionClassName = $adapter->getExceptionForErrorCode($this->_lastErrorCode());
            throw new $exceptionClassName("Exception: query: " . $this->_querySQLWithParams . " failed: " . $this->_lastErrorMessage());
        }
    }

    public static function prepare($preparedStatement, $querySqlWithParams)
    {
        return new self($preparedStatement, $querySqlWithParams);
    }
}