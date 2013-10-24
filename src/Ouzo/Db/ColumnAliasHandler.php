<?php
namespace Ouzo\Db;

use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

class ColumnAliasHandler
{
    public static function createSelectColumnsWithAliases($prefix, $columns, $table)
    {
        return FluentArray::from($columns)->toMap(
            function ($field) use ($prefix) {
                return "{$prefix}{$field}";
            },
            function ($field) use ($table) {
                return "$table.$field";
            }
        )->toArray();
    }

    public static function extractAttributesForPrefix($result, $prefix)
    {
        return FluentArray::from($result)
            ->filterByKeys(Functions::startsWith($prefix))
            ->mapKeys(Functions::removePrefix($prefix))
            ->toArray();
    }
}