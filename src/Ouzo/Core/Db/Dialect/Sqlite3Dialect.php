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
    /**
     * @inheritdoc
     */
    public function getConnectionErrorCodes()
    {
        return [10, 11, 14];
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 1);
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function update()
    {
        if ($this->query->aliasTable) {
            throw new InvalidArgumentException("Alias in update query is not supported in sqlite3");
        }
        return parent::update();
    }

    /**
     * @inheritdoc
     * @throws BadMethodCallException
     */
    public function join()
    {
        $any = Arrays::any($this->query->joinClauses, function (JoinClause $joinClause) {
            return Strings::equalsIgnoreCase($joinClause->type, 'RIGHT');
        });
        if ($any) {
            throw new BadMethodCallException('RIGHT JOIN is not supported in sqlite3');
        }
        return parent::join();
    }

    /**
     * @inheritdoc
     * @throws BadMethodCallException
     */
    public function lockForUpdate()
    {
        if ($this->query->lockForUpdate) {
            throw new BadMethodCallException('SELECT ... FOR UPDATE is not supported in sqlite3');
        }
    }

    /**
     * @inheritdoc
     * @throws BadMethodCallException
     */
    public function using()
    {
        if ($this->query->usingClauses) {
            throw new BadMethodCallException('USING clause is not supported in sqlite3');
        }
    }

    /**
     * @inheritdoc
     * @throws InvalidArgumentException
     */
    public function batchInsert($table, $primaryKey, $columns, $batchSize)
    {
        throw new InvalidArgumentException("Batch insert not supported in sqlite3");
    }

    /**
     * @inheritdoc
     */
    protected function insertEmptyRow()
    {
        return "INSERT INTO {$this->query->table} DEFAULT VALUES";
    }

    /**
     * @inheritdoc
     */
    public function regexpMatcher()
    {
        //needs package sqlite3-pcre to work correctly
        return 'REGEXP';
    }

    /**
     * @inheritdoc
     */
    protected function quote($word)
    {
        return '"' . $word . '"';
    }

    /**
     * @inheritdoc
     */
    public function getIteratorOptions()
    {
        return [];
    }


    /**
     * @return string
     */
    public function onConflictUpdate()
    {
        throw new BadMethodCallException('UPSERT is not supported in sqlite3');
    }

    /**
     * @return string
     */
    public function onConflictDoNothing()
    {
        throw new BadMethodCallException('On conflict do nothing is not supported in sqlite3');
    }
}
