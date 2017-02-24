<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

class ColumnAliasHandler
{
    public static function createSelectColumnsWithAliases($columns, $alias)
    {
        return Arrays::map($columns, function ($field) use ($alias) {
            return "$alias.$field";
        });
    }
}
