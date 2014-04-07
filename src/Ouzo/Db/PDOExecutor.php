<?php

namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

abstract class PDOExecutor
{
    public abstract function createPDOStatement($dbHandle, $sql, $boundValues, $queryString);

    public static function newInstance($options)
    {
        if (Arrays::getValue($options, Options::EMULATE_PREPARES)) {
            return new EmulatedPDOPreparedStatementExecutor();
        }
        return new PDOPreparedStatementExecutor();
    }
} 