<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use PDO;

class ParameterType
{
    public static function getType(mixed $param): int
    {
        if (is_int($param)) {
            return PDO::PARAM_INT;
        }
        if (is_bool($param)) {
            return PDO::PARAM_BOOL;
        }
        return PDO::PARAM_STR;
    }
}
