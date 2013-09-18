<?php

namespace Ouzo\Db;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Strings;

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

    public static function removeAliasesFromResults($results, $prefix)
    {
        return Arrays::map($results, function ($result) use ($prefix) {
            return self::removeAliasesFromResult($result, $prefix);
        });
    }

    public static function removeAliasesFromResult($result, $prefix)
    {
        return FluentArray::from($result)
            ->mapKeys(Functions::removePrefix($prefix))
            ->toArray();
    }

    public static function extractAttributesForPrefix($result, $prefix)
    {
        return FluentArray::from($result)
            ->filterByKeys(Functions::startsWith($prefix))
            ->mapKeys(Functions::removePrefix($prefix))
            ->toArray();
    }
}