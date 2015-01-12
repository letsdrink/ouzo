<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use BadMethodCallException;
use Ouzo\Db\JoinClause;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Sqlite3Dialect extends Dialect
{
    public function getConnectionErrorCodes()
    {
        return array(10, 11, 14);
    }

    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 1);
    }

    public function update()
    {
        if ($this->_query->aliasTable) {
            throw new \InvalidArgumentException("Alias in update query is nut supported in sqlite");
        }
        return parent::update();
    }

    public function join()
    {
        $any = Arrays::any($this->_query->joinClauses, function (JoinClause $joinClause) {
            return Strings::equalsIgnoreCase($joinClause->type, 'RIGHT');
        });
        if ($any) {
            throw new BadMethodCallException('RIGHT JOIN is not supported in sqlite3.');
        }
        return parent::join();
    }
}
