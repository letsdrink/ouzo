<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Utilities\FluentArray;

class ColumnAliasHandler
{
    public static function createSelectColumnsWithAliases($prefix, $columns, $alias)
    {
        return FluentArray::from($columns)->toMap(
            function ($field) use ($prefix) {
                return "{$prefix}{$field}";
            },
            function ($field) use ($alias) {
                return "$alias.$field";
            }
        )->toArray();
    }
}
