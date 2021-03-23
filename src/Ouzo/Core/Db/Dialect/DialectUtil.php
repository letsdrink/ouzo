<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\Dialect;

use Closure;
use Ouzo\Db\JoinClause;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Joiner;

class DialectUtil
{
    public static function addAliases(): Closure
    {
        return fn($alias, $column) => $column . (is_string($alias) ? " AS {$alias}" : '');
    }

    /** @param WhereClause[] $whereClauses */
    public static function buildWhereQuery(array $whereClauses): string
    {
        $parts = FluentArray::from($whereClauses)
            ->filter(WhereClause::isNotEmptyFunction())
            ->map([DialectUtil::class, 'buildWhereQueryPart'])
            ->toArray();
        return implode(' AND ', Arrays::filterNotBlank($parts));
    }

    public static function buildWhereQueryPart(WhereClause $whereClause): string
    {
        return $whereClause->toSql();
    }

    /** @param JoinClause[] $joinClauses */
    public static function buildJoinQuery(array $joinClauses): string
    {
        $elements = FluentArray::from($joinClauses)
            ->map([DialectUtil::class, 'buildJoinQueryPart'])
            ->toArray();
        return implode(" ", $elements);
    }

    public static function buildJoinQueryPart(JoinClause $joinClause): string
    {
        $alias = $joinClause->alias ? " AS {$joinClause->alias}" : "";
        $on = self::buildWhereQuery(Arrays::toArray($joinClause->onClauses));
        if ($joinClause->alias) {
            $on = preg_replace("#(?<=^| ){$joinClause->joinTable}(?=\\.)#", $joinClause->alias, $on);
        }
        $onClause = $on ? " AND {$on}" : '';
        return "{$joinClause->type} JOIN {$joinClause->joinTable}{$alias} ON {$joinClause->getJoinColumnWithTable()} = {$joinClause->getJoinedColumnWithTable()}{$onClause}";
    }

    public static function buildAttributesPartForUpdate(array $updateAttributes): string
    {
        return Joiner::on(', ')->join(FluentArray::from($updateAttributes)
            ->keys()
            ->map(fn($column) => "$column = ?")->toArray());
    }

    /** @param JoinClause[] $usingClauses */
    public static function buildUsingQuery(array $usingClauses, string $glue, ?string $table, ?string $alias): string
    {
        $elements = FluentArray::from($usingClauses)
            ->map([DialectUtil::class, 'buildUsingQueryPart'])
            ->toArray();
        if ($usingClauses && $table) {
            $tableElement = $table . ($alias ? " AS {$alias}" : "");
            $elements = array_merge([$tableElement], $elements);
        }
        return implode($glue, $elements);
    }

    public static function buildUsingQueryPart(JoinClause $usingClause): string
    {
        $alias = $usingClause->alias ? " AS {$usingClause->alias}" : "";
        return $usingClause->joinTable . $alias;
    }

    public static function joinClauses(array $parts, string $operator, ?Closure $extractFunction = null): string
    {
        $mappedParts = $extractFunction ? Arrays::map($parts, $extractFunction) : $parts;
        $sql = implode(" $operator ", $mappedParts);
        return $operator === 'OR' && count($parts) > 1 ? "($sql)" : $sql;
    }
}
