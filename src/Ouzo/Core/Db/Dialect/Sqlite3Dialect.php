<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use BadMethodCallException;
use InvalidArgumentException;
use Ouzo\Db\JoinClause;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Sqlite3Dialect extends Dialect
{
    public function getConnectionErrorCodes()
    {
        return [10, 11, 14];
    }

    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 1);
    }

    public function update()
    {
        if ($this->_query->aliasTable) {
            throw new InvalidArgumentException("Alias in update query is not supported in sqlite3");
        }
        return parent::update();
    }

    public function join()
    {
        $any = Arrays::any($this->_query->joinClauses, function (JoinClause $joinClause) {
            return Strings::equalsIgnoreCase($joinClause->type, 'RIGHT');
        });
        if ($any) {
            throw new BadMethodCallException('RIGHT JOIN is not supported in sqlite3');
        }
        return parent::join();
    }

    public function lockForUpdate()
    {
        if ($this->_query->lockForUpdate) {
            throw new BadMethodCallException('SELECT ... FOR UPDATE is not supported in sqlite3');
        }
    }

    public function using()
    {
        if ($this->_query->usingClauses) {
            throw new BadMethodCallException('USING clause is not supported in sqlite3');
        }
    }

    public function batchInsert($table, $primaryKey, $columns, $batchSize)
    {
        throw new InvalidArgumentException("Batch insert not supported in sqlite3");
    }

    protected function insertEmptyRow()
    {
        return "INSERT INTO {$this->_query->table} DEFAULT VALUES";
    }

    public function regexpMatcher()
    {
        //needs package sqlite3-pcre to work correctly
        return 'REGEXP';
    }

    protected function quote($word)
    {
        return '"' . $word . '"';
    }
}
