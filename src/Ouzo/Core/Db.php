<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;
use Ouzo\Db\PDOExceptionExtractor;
use Ouzo\Db\StatementExecutor;
use Ouzo\Db\TransactionalProxy;
use Ouzo\Utilities\Arrays;
use PDO;

class Db
{
    /**
     * @var PDO
     */
    public $_dbHandle = null;
    public $_startedTransaction = false;

    private static $_instance;

    public function __construct($loadDefault = true)
    {
        if ($loadDefault) {
            $configDb = Config::getValue('db');
            if (!empty($configDb)) {
                $this->connectDb($configDb);
            }
        }
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function connectDb($params = array())
    {
        $this->_dbHandle = $this->_createPdo($params);
        $attributes = Arrays::getValue($params, 'attributes', array());
        foreach ($attributes as $attribute => $value) {
            $this->_dbHandle->setAttribute($attribute, $value);
        }
        return $this;
    }

    public static function callFunction($functionName, $parameters)
    {
        $db = self::getInstance();
        $bindParams = Arrays::toArray($parameters);
        $paramsQueryString = implode(',', array_pad(array(), sizeof($bindParams), '?'));
        return Arrays::first($db->query("SELECT $functionName($paramsQueryString)", $parameters)->fetch());
    }

    public function query($query, $params = array(), $options = array())
    {
        return StatementExecutor::prepare($this->_dbHandle, $query, $params, $options);
    }

    /**
     * Returns number of affected rows
     */
    public function execute($query, $params = array(), $options = array())
    {
        return StatementExecutor::prepare($this->_dbHandle, $query, $params, $options)->execute();
    }

    /**
     * Returns a new transactional proxy for given target object.
     * All methods called on proxy are run in a transaction.
     */
    public static function transactional($object)
    {
        return TransactionalProxy::newInstance($object);
    }

    public function runInTransaction($callable)
    {
        if (!$this->_startedTransaction) {
            $this->beginTransaction();
            try {
                $result = call_user_func($callable);
                $this->commitTransaction();
                return $result;
            } catch (Exception $e) {
                $this->rollbackTransaction();
                throw $e;
            }
        }
        return call_user_func($callable);
    }

    public function beginTransaction()
    {
        $this->_startedTransaction = true;
        $this->_dbHandle->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->_dbHandle->commit();
        $this->_startedTransaction = false;
    }

    public function rollbackTransaction()
    {
        $this->_dbHandle->rollBack();
        $this->_startedTransaction = false;
    }

    public function lastErrorMessage()
    {
        $errorInfo = $this->_dbHandle->errorInfo();
        return $errorInfo[2];
    }

    private function _buildDsn($params)
    {
        $charset = Arrays::getValue($params, 'charset');
        $dsn = $params['driver'] . ':host=' . $params['host'] . ';port=' . $params['port'] . ';dbname=' . $params['dbname'] . ';user=' . $params['user'] . ';password=' . $params['pass'];
        return $dsn . ($charset ? ';charset=' . $charset : '');
    }

    private function _createPdo($params)
    {
        $dsn = Arrays::getValue($params, 'dsn');
        if ($dsn) {
            return new PDO($dsn);
        }
        $dsn = $this->_buildDsn($params);
        return new PDO($dsn, $params['user'], $params['pass']);
    }

    public function lastInsertId($sequence)
    {
        $lastInsertId = $this->_dbHandle->lastInsertId($sequence);
        if (!$lastInsertId) {
            throw PDOExceptionExtractor::getException($this->_dbHandle->errorInfo(), "Cannot get sequence value: $sequence");
        }
        return $lastInsertId;
    }
}
