<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\Dialect;

use BadMethodCallException;
use InvalidArgumentException;
use Ouzo\Db\JoinClause;
use Ouzo\Db\OnConflict;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Sqlite3Dialect extends Dialect
{
    public function getConnectionErrorCodes(): array
    {
        return [10, 11, 14];
    }

    public function getErrorCode(array $errorInfo): mixed
    {
        return Arrays::getValue($errorInfo, 1);
    }

    public function update(): string
    {
        if ($this->query->aliasTable) {
            throw new InvalidArgumentException("Alias in update query is not supported in sqlite3");
        }
        return parent::update();
    }

    public function join(): string
    {
        $any = Arrays::any($this->query->joinClauses, function (JoinClause $joinClause) {
            return Strings::equalsIgnoreCase($joinClause->type, 'RIGHT');
        });
        if ($any) {
            throw new BadMethodCallException('RIGHT JOIN is not supported in sqlite3');
        }
        return parent::join();
    }

    public function lockForUpdate(): string
    {
        if ($this->query->lockForUpdate) {
            throw new BadMethodCallException('SELECT ... FOR UPDATE is not supported in sqlite3');
        }
        return '';
    }

    public function using(): string
    {
        if ($this->query->usingClauses) {
            throw new BadMethodCallException('USING clause is not supported in sqlite3');
        }
        return '';
    }

    public function batchInsert(string $table, string $primaryKey, $columns, $batchSize, ?OnConflict $onConflict): string
    {
        throw new InvalidArgumentException("Batch insert not supported in sqlite3");
    }

    protected function insertEmptyRow(): string
    {
        return "INSERT INTO {$this->query->table} DEFAULT VALUES";
    }

    public function regexpMatcher(): string
    {
        //needs package sqlite3-pcre to work correctly
        return 'REGEXP';
    }

    protected function quote(string $word): string
    {
        return "\"{$word}\"";
    }

    public function getIteratorOptions(): array
    {
        return [];
    }

    public function onConflictUpdate(): string
    {
        throw new BadMethodCallException('UPSERT is not supported in sqlite3');
    }

    public function onConflictDoNothing(): string
    {
        throw new BadMethodCallException('On conflict do nothing is not supported in sqlite3');
    }

    protected function getDistinctOnQuery(): string
    {
        throw new InvalidArgumentException("DISTINCT ON is not supported in sqlite3");
    }
}
