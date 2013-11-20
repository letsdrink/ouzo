<?php

namespace Ouzo\Db;

use Ouzo\Config;
use Ouzo\Utilities\Arrays;

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

    public function execute()
    {
        if (!$this->_preparedStatement->execute()) {
            $dialect = Config::getValue('sql_dialect');
            $adapter = new $dialect();
            $errorInfo = $this->_preparedStatement->errorInfo();
            $exceptionClassName = $adapter->getExceptionForError($errorInfo);
            throw new $exceptionClassName(sprintf("Exception: query: %s failed: %s (%s)",
                $this->_querySQLWithParams,
                $this->_errorMessageFromErrorInfo($errorInfo),
                $this->_errorCodesFromErrorInfo($errorInfo)
            ));
        }
    }

    public static function prepare($preparedStatement, $querySqlWithParams)
    {
        return new self($preparedStatement, $querySqlWithParams);
    }

    private function _errorMessageFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 2);
    }

    private function _errorCodesFromErrorInfo($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0) . "_" . Arrays::getValue($errorInfo, 1);
    }
}