<?php
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