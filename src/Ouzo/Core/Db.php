<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Closure;
use Exception;
use Ouzo\Db\PDOExceptionExtractor;
use Ouzo\Db\StatementExecutor;
use Ouzo\Db\TransactionalProxy;
use Ouzo\Utilities\Arrays;
use PDO;
use PDOException;


class Db
{
    public ?PDO $dbHandle = null;
    public bool $startedTransaction = false;

    private static ?Db $instance = null;
    private static bool $transactionsEnabled = true;

    public function __construct(bool $loadDefault = true)
    {
        if ($loadDefault) {
            $configDb = Config::getValue('db');
            if (!empty($configDb)) {
                $this->connectDb($configDb);
            }
        }
    }

    public static function getInstance(): Db
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connectDb(array $params = []): static
    {
        $this->dbHandle = $this->createPdo($params);
        $attributes = Arrays::getValue($params, 'attributes', []);
        foreach ($attributes as $attribute => $value) {
            $this->dbHandle->setAttribute($attribute, $value);
        }
        return $this;
    }

    public static function callFunction(string $functionName, ?array $parameters): mixed
    {
        $db = self::getInstance();
        $bindParams = Arrays::toArray($parameters);
        $paramsQueryString = implode(',', array_pad([], sizeof($bindParams), '?'));
        return Arrays::first($db->query("SELECT $functionName($paramsQueryString)", $parameters)->fetch());
    }

    public function query(string $query, array $params = [], array $options = []): StatementExecutor
    {
        return StatementExecutor::prepare($this->dbHandle, $query, $params, $options);
    }

    /**
     * Returns number of affected rows
     */
    public function execute(string $query, array $params = [], array $options = []): int
    {
        return StatementExecutor::prepare($this->dbHandle, $query, $params, $options)->execute();
    }

    /**
     * Returns a new transactional proxy for given target object/function.
     * All methods called on proxy are run in a transaction.
     */
    public static function transactional(mixed $object): TransactionalProxy
    {
        return TransactionalProxy::newInstance($object);
    }

    public function runInTransaction(Closure $callable): mixed
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

    public function beginTransaction(): void
    {
        if (self::$transactionsEnabled) {
            $this->startedTransaction = true;
            $this->invokePdo('beginTransaction');
        }
    }

    public function commitTransaction(): void
    {
        if (self::$transactionsEnabled) {
            $this->invokePdo('commit');
            $this->startedTransaction = false;
        }
    }

    public function rollbackTransaction(): void
    {
        if (self::$transactionsEnabled) {
            $this->invokePdo('rollBack');
            $this->startedTransaction = false;
        }
    }

    private function rollbackTransactionSilently(): void
    {
        try {
            $this->rollbackTransaction();
        } catch (Exception) {
        }
    }

    private function invokePdo(string $method): void
    {
        $result = call_user_func([$this->dbHandle, $method]);
        if ($result === false) {
            $info = $this->dbHandle->errorInfo();
            $sqlState = Arrays::getValue($info, 0);
            $code = Arrays::getValue($info, 1);
            $message = Arrays::getValue($info, 2);
            throw new DbException("Pdo method '{$method}' failed. Message: '{$message}'. Code: '{$code}'. SqlState code: '{$sqlState}'", $code);
        }
    }

    public function lastErrorMessage(): string
    {
        $errorInfo = $this->dbHandle->errorInfo();
        return $errorInfo[2];
    }

    private function buildDsn(array $params): string
    {
        $charset = Arrays::getValue($params, 'charset');
        $dsn = "{$params['driver']}:host={$params['host']};port={$params['port']};dbname={$params['dbname']};user={$params['user']};password={$params['pass']}";
        return $dsn . ($charset ? ';charset=' . $charset : '');
    }

    private function createPdo(array $params): PDO
    {
        $dsn = Arrays::getValue($params, 'dsn');
        $options = Arrays::getValue($params, 'options', []);
        if ($dsn) {
            return new PDO($dsn, '', '', $options);
        }
        $dsn = $this->buildDsn($params);
        return new PDO($dsn, $params['user'], $params['pass'], $options);
    }

    public function lastInsertId(?string $sequence): string
    {
        try {
            $lastInsertId = $this->dbHandle->lastInsertId($sequence);
            if (!$lastInsertId) {
                throw PDOExceptionExtractor::getException($this->dbHandle->errorInfo(), "Cannot get sequence value: {$sequence}");
            }
            return $lastInsertId;
        } catch (PDOException $exception) {
            $errorInfo = [$exception->getCode(), $exception->getCode(), $exception->getMessage()];
            throw PDOExceptionExtractor::getException($errorInfo, "Cannot get sequence value: {$sequence}");
        }
    }

    public function disableTransactions(): void
    {
        self::$transactionsEnabled = false;
    }

    public function enableTransactions(): void
    {
        self::$transactionsEnabled = true;
    }

    public function isConnected(): bool
    {
        return $this->dbHandle != null;
    }
}
