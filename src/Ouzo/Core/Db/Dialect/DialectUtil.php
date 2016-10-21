<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use Ouzo\Db\JoinClause;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Joiner;

class DialectUtil
{
    public static function _addAliases()
    {
        return function ($alias, $column) {
            return $column . (is_string($alias) ? ' AS ' . $alias : '');
        };
    }

    public static function buildWhereQuery($whereClauses)
    {
        $parts = FluentArray::from($whereClauses)
            ->filter(WhereClause::isNotEmptyFunction())
            ->map('\Ouzo\Db\Dialect\DialectUtil::buildWhereQueryPart')
            ->toArray();
        return implode(' AND ', Arrays::filterNotBlank($parts));
    }

    public static function buildWhereQueryPart(WhereClause $whereClause)
    {
        return $whereClause->toSql();
    }

    public static function buildJoinQuery($joinClauses)
    {
        $elements = FluentArray::from($joinClauses)
            ->map('\Ouzo\Db\Dialect\DialectUtil::buildJoinQueryPart')
            ->toArray();
        return implode(" ", $elements);
    }

    public static function buildJoinQueryPart(JoinClause $joinClause)
    {
        $alias = $joinClause->alias ? " AS {$joinClause->alias}" : "";
        $on = self::buildWhereQuery($joinClause->onClauses);
        if ($joinClause->alias) {
            $on = preg_replace("#(?<=^| ){$joinClause->joinTable}(?=\\.)#", $joinClause->alias, $on);
        }
        return $joinClause->type . ' JOIN ' . $joinClause->joinTable . $alias . ' ON ' . $joinClause->getJoinColumnWithTable() . ' = ' . $joinClause->getJoinedColumnWithTable() . ($on ? " AND $on" : '');
    }

    public static function buildAttributesPartForUpdate($updateAttributes)
    {
        return Joiner::on(', ')->join(FluentArray::from($updateAttributes)
            ->keys()
            ->map(function ($column) {
                return "$column = ?";
            })->toArray());
    }

    public static function buildUsingQuery($usingClauses, $glue, $table, $alias)
    {
        $elements = FluentArray::from($usingClauses)
            ->map('\Ouzo\Db\Dialect\DialectUtil::buildUsingQueryPart')
            ->toArray();
        if ($usingClauses && $table) {
            $tableElement = $table . ($alias ? " AS {$alias}" : "");
            $elements = array_merge(array($tableElement), $elements);
        }
        return implode($glue, $elements);
    }

    public static function buildUsingQueryPart(JoinClause $usingClause)
    {
        $alias = $usingClause->alias ? " AS {$usingClause->alias}" : "";
        return $usingClause->joinTable . $alias;
    }

    public static function joinClauses($parts, $operator, $extractFunction = null)
    {
        $mappedParts = $extractFunction ? Arrays::map($parts, $extractFunction) : $parts;
        $sql = implode(" $operator ", $mappedParts);
        return $operator == 'OR' && count($parts) > 1 ? "($sql)" : $sql;
    }
}
