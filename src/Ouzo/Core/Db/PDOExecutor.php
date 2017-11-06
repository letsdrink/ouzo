<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;
use PDO;
use PDOStatement;

abstract class PDOExecutor
{
    /**
     * @param PDO $dbHandle
     * @param string $sql
     * @param array $boundValues
     * @param string $queryString
     * @param array $options
     * @return PDOStatement
     */
    abstract public function createPDOStatement($dbHandle, $sql, $boundValues, $queryString, $options = []);

    /**
     * @param array $options
     * @return PDOExecutor
     */
    public static function newInstance(array $options)
    {
        if (Arrays::getValue($options, Options::EMULATE_PREPARES)) {
            return new EmulatedPDOPreparedStatementExecutor();
        }
        return new PDOPreparedStatementExecutor();
    }
}
