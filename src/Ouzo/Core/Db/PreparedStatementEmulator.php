<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

class PreparedStatementEmulator
{
    /**
     * @param string $sql
     * @param array $params
     * @return string
     */
    public static function substitute($sql, $params)
    {
        return preg_replace_callback('/[\'?]/', new ParameterPlaceHolderCallback($params), $sql);
    }
}
