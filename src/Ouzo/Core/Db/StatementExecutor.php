<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Closure;
use Ouzo\Config;
use Ouzo\Logger\Backtrace;
use Ouzo\Logger\Logger;
use Ouzo\Utilities\Objects;
use PDO;
use PDOStatement;

class StatementExecutor
{
    private string $humanizedSql;

    private function __construct(
        private PDO $dbHandle,
        private string $sql,
        private array $boundValues,
        private PDOExecutor $pdoExecutor
    )
    {
        $this->humanizedSql = QueryHumanizer::humanize($sql);
    }

    private function executeWithStats(Closure $afterCallback): mixed
    {
        return Stats::trace($this->humanizedSql, $this->boundValues, fn() => $this->internalExecute($afterCallback));
    }

    private function internalExecute(Closure $afterCallback): mixed
    {
        $pdoStatement = $this->createPdoStatement();
        $result = call_user_func($afterCallback, $pdoStatement);
        $pdoStatement->closeCursor();
        return $result;
    }

    public function execute(): mixed
    {
        return $this->executeWithStats(fn(PDOStatement $pdoStatement) => $pdoStatement->rowCount());
    }

    public function executeAndFetch(string $function, string $fetchStyle): mixed
    {
        return $this->executeWithStats(fn($pdoStatement) => $pdoStatement->$function($fetchStyle));
    }

    public function fetch(int $fetchMode = PDO::FETCH_ASSOC): mixed
    {
        return $this->executeAndFetch('fetch', $fetchMode);
    }

    public function fetchAll(int $fetchMode = PDO::FETCH_ASSOC): mixed
    {
        return $this->executeAndFetch('fetchAll', $fetchMode);
    }

    public static function prepare(PDO $dbHandle, string $sql, array $boundValues, array $options): StatementExecutor
    {
        $pdoExecutor = PDOExecutor::newInstance($options);
        return new StatementExecutor($dbHandle, $sql, $boundValues, $pdoExecutor);
    }

    public function fetchIterator(array $options = []): StatementIterator
    {
        return Stats::trace($this->humanizedSql, $this->boundValues, function () use ($options) {
            $pdoStatement = $this->createPdoStatement($options);
            return new StatementIterator($pdoStatement);
        });
    }

    public function createPdoStatement(array $options = []): PDOStatement
    {
        $sqlString = $this->prepareSqlString();

        $callingClass = Backtrace::getCallingClass();
        Logger::getLogger(__CLASS__)->asLoggerInterface()->info("From: {$callingClass} Query: {$sqlString}");

        return $this->pdoExecutor->createPDOStatement($this->dbHandle, $this->sql, $this->boundValues, $sqlString, $options);
    }

    private function prepareSqlString(): string
    {
        $truncateLimit = Config::getValue('db', 'truncate_bound_values_string_limit');

        $boundValuesAsString = Objects::toString($this->boundValues);
        $boundValuesAsStringLength = mb_strlen($boundValuesAsString);
        if (!is_null($truncateLimit) && $boundValuesAsStringLength > $truncateLimit) {
            $boundValuesAsString = trim(mb_substr($boundValuesAsString, 0, $truncateLimit)) . "...\"] (truncated from {$boundValuesAsStringLength})";
        }

        return sprintf("%s with params: %s", $this->humanizedSql, $boundValuesAsString);
    }
}
