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
    /** @var PDO */
    public $dbHandle = null;
    /** @var bool */
    public $startedTransaction = false;

    /** @var Db */
    private static $instance = null;
    /** @var bool */
    private static $transactionsEnabled = true;

    /**
     * @param bool $loadDefault
     */
    public function __construct($loadDefault = true)
    {
        if ($loadDefault) {
            $configDb = Config::getValue('db');
            if (!empty($configDb)) {
                $this->connectDb($configDb);
            }
        }
    }

    /**
     * @return Db
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function connectDb($params = [])
    {
        $this->dbHandle = $this->createPdo($params);
        $attributes = Arrays::getValue($params, 'attributes', []);
        foreach ($attributes as $attribute => $value) {
            $this->dbHandle->setAttribute($attribute, $value);
        }
        return $this;
    }

    /**
     * @param string $functionName
     * @param array $parameters
     * @return mixed
     */
    public static function callFunction($functionName, $parameters)
    {
        $db = self::getInstance();
        $bindParams = Arrays::toArray($parameters);
        $paramsQueryString = implode(',', array_pad([], sizeof($bindParams), '?'));
        return Arrays::first($db->query("SELECT $functionName($paramsQueryString)", $parameters)->fetch());
    }

    /**
     * @param string $query
     * @param array $params
     * @param array $options
     * @return StatementExecutor
     */
    public function query($query, $params = [], $options = [])
    {
        return StatementExecutor::prepare($this->dbHandle, $query, $params, $options);
    }

    /**
     * Returns number of affected rows
     * @param string $query
     * @param array $params
     * @param array $options
     * @return int
     */
    public function execute($query, $params = [], $options = [])
    {
        return StatementExecutor::prepare($this->dbHandle, $query, $params, $options)->execute();
    }

    /**
     * Returns a new transactional proxy for given target object/function.
     * All methods called on proxy are run in a transaction.
     * @param mixed $object
     * @return TransactionalProxy
     */
    public static function transactional($object)
    {
        return TransactionalProxy::newInstance($object);
    }

    /**
     * @param \Closure $callable
     * @return mixed
     * @throws Exception
     */
    public function runInTransaction($callable)
    {
        if (!$this->startedTransaction) {
            $this->beginTransaction();
            try {
                $result = call_user_func($callable);
                $this->commitTransaction();
                return $result;
            } catch (Exception $e) {
                $this->rollbackTransactionSilently();
                throw $e;
            }
        }
        return call_user_func($callable);
    }

    /**
     * @return void
     */
    public function beginTransaction()
    {
        if (self::$transactionsEnabled) {
            $this->startedTransaction = true;
            $this->invokePdo('beginTransaction');
        }
    }

    /**
     * @return void
     */
    public function commitTransaction()
    {
        if (self::$transactionsEnabled) {
            $this->invokePdo('commit');
            $this->startedTransaction = false;
        }
    }

    /**
     * @return void
     */
    public function rollbackTransaction()
    {
        if (self::$transactionsEnabled) {
            $this->invokePdo('rollBack');
            $this->startedTransaction = false;
        }
    }

    /**
     * @return void
     */
    private function rollbackTransactionSilently()
    {
        try {
            $this->rollbackTransaction();
        } catch (Exception $e) {
        }
    }

    /**
     * @param $method
     * @return void
     */
    private function invokePdo($method)
    {
        $result = call_user_func([$this->dbHandle, $method]);
        if ($result === false) {
            $info = $this->dbHandle->errorInfo();
            $sqlState = Arrays::getValue($info, 0);
            $code = Arrays::getValue($info, 1);
            $message = Arrays::getValue($info, 2);
            throw new DbException("Pdo method '$method' failed. Message: '$message'. Code: '$code'. SqlState code: '$sqlState'", $code);
        }
    }

    /**
     * @return string
     */
    public function lastErrorMessage()
    {
        $errorInfo = $this->dbHandle->errorInfo();
        return $errorInfo[2];
    }

    /**
     * @param array $params
     * @return string
     */
    private function buildDsn(array $params)
    {
        $charset = Arrays::getValue($params, 'charset');
        $dsn = $params['driver'] . ':host=' . $params['host'] . ';port=' . $params['port'] . ';dbname=' . $params['dbname'] . ';user=' . $params['user'] . ';password=' . $params['pass'];
        return $dsn . ($charset ? ';charset=' . $charset : '');
    }

    /**
     * @param array $params
     * @return PDO
     */
    private function createPdo(array $params)
    {
        $dsn = Arrays::getValue($params, 'dsn');
        $options = Arrays::getValue($params, 'options', []);
        if ($dsn) {
            return new PDO($dsn, '', '', $options);
        }
        $dsn = $this->buildDsn($params);
        return new PDO($dsn, $params['user'], $params['pass'], $options);
    }

    /**
     * @param string $sequence
     * @return string
     * @throws Exception
     */
    public function lastInsertId($sequence)
    {
        $lastInsertId = $this->dbHandle->lastInsertId($sequence);
        if (!$lastInsertId) {
            throw PDOExceptionExtractor::getException($this->dbHandle->errorInfo(), "Cannot get sequence value: $sequence");
        }
        return $lastInsertId;
    }

    /**
     * @return void
     */
    public function disableTransactions()
    {
        self::$transactionsEnabled = false;
    }

    /**
     * @return void
     */
    public function enableTransactions()
    {
        self::$transactionsEnabled = true;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->dbHandle != null;
    }
}
