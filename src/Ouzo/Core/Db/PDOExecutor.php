<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

abstract class PDOExecutor
{
    abstract public function createPDOStatement($dbHandle, $sql, $boundValues, $queryString);

    public static function newInstance($options)
    {
        if (Arrays::getValue($options, Options::EMULATE_PREPARES)) {
            return new EmulatedPDOPreparedStatementExecutor();
        }
        return new PDOPreparedStatementExecutor();
    }
}
