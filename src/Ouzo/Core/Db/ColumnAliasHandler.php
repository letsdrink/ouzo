<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Utilities\Arrays;

class ColumnAliasHandler
{
    /** @param string[] $columns */
    public static function createSelectColumnsWithAliases(array $columns, string $alias): array
    {
        return Arrays::map($columns, fn($field) => "{$alias}.{$field}");
    }
}
