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
    abstract public function createPDOStatement(PDO $dbHandle, string $sql, array $boundValues, string $queryString, array $options = []): PDOStatement;

    public static function newInstance(array $options): PDOExecutor
    {
        if (Arrays::getValue($options, Options::EMULATE_PREPARES)) {
            return new EmulatedPDOPreparedStatementExecutor();
        }
        return new PDOPreparedStatementExecutor();
    }
}
