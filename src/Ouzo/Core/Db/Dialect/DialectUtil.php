<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use Ouzo\Db\Any;
use Ouzo\Db\JoinClause;
use Ouzo\Db\WhereClause;
use Ouzo\Restriction\Restriction;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Strings;

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
        return implode(' AND ', $parts);
    }

    public static function buildWhereQueryPart($whereClause)
    {
        if ($whereClause->where instanceof Any) {
            return '(' . implode(' OR ', self::_buildWhereKeys($whereClause->where->getConditions())) . ')';
        }
        $wherePart = is_array($whereClause->where) ? implode(' AND ', self::_buildWhereKeys($whereClause->where)) : self::_buildWhereKeyFromString($whereClause);
        return stripos($wherePart, 'OR') ? '(' . $wherePart . ')' : $wherePart;
    }

    private static function _buildWhereKeys($params)
    {
        $keys = array();
        foreach ($params as $key => $value) {
            $keys[] = self::_buildWhereKey($value, $key);
        }
        return $keys;
    }

    private static function _buildWhereKey($value, $key)
    {
        if (is_array($value)) {
            $in = implode(', ', array_fill(0, count($value), '?'));
            return $key . ' IN (' . $in . ')';
        }
        if ($value === null) {
            return $key . ' IS NULL';
        }
        if ($value instanceof Restriction) {
            return $value->toSql($key);
        }
        return $key . ' = ?';
    }

    private static function _buildWhereKeyFromString($whereClause)
    {
        $wherePart = $whereClause->where;
        $values = Arrays::toArray($whereClause->values);
        $any = Arrays::any($values, function ($value) {
            return $value === null;
        });
        if ($any) {
            $keyWithNull = array_search(null, $values);
            $wherePart = Strings::replaceNth($wherePart, '\\=\\s*\\?', 'IS NULL', $keyWithNull);
        }
        return $wherePart;
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
}
