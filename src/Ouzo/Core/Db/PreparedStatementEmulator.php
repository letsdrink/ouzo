<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

class PreparedStatementEmulator
{
    public static function substitute(string $sql, array $params): string
    {
        return preg_replace_callback('/[\'?]/', new ParameterPlaceHolderCallback($params), $sql);
    }
}
