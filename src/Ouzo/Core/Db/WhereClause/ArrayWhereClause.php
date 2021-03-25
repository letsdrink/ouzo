<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

use Ouzo\Db\Dialect\DialectUtil;
use Ouzo\Restriction\Restriction;
use Ouzo\Restrictions;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class ArrayWhereClause extends WhereClause
{
    private array $where;
    private array $values;
    private string $operator;

    public function __construct(array $where, string $operator = 'AND')
    {
        foreach ($where as $column => $value) {
            if ($value === null) {
                $where[$column] = Restrictions::isNull();
            }
        }
        $this->where = $where;
        $this->values = Arrays::flatten(array_values($where));
        $this->operator = $operator;
    }

    public function isNeverSatisfied(): bool
    {
        return Arrays::any($this->where, fn($value) => is_array($value) && sizeof($value) == 0);
    }

    public function isEmpty(): bool
    {
        return empty($this->where);
    }

    public function toSql(): string
    {
        $whereKeys = self::buildWhereKeys($this->where);
        return DialectUtil::joinClauses($whereKeys, $this->operator);
    }

    public function getParameters(): array
    {
        return $this->values;
    }

    private static function buildWhereKeys(array $params): array
    {
        return Arrays::filterNotBlank(Arrays::mapEntries($params, fn($column, $value) => self::buildWhereKey($column, $value)));
    }

    private static function buildWhereKey(string $column, mixed $value): string
    {
        if (is_array($value)) {
            return self::buildWhereKeyIn($column, $value);
        }
        if ($value instanceof Restriction) {
            return $value->toSql($column);
        }
        return $column . ' = ?';
    }

    private static function buildWhereKeyIn(string $column, array $array): string
    {
        $useRestrictions = Arrays::any($array, Functions::isInstanceOf(Restriction::class));

        if ($useRestrictions) {
            return DialectUtil::joinClauses($array, 'OR', fn(Restriction $restriction) => $restriction->toSql($column));
        }

        $in = implode(', ', array_fill(0, count($array), '?'));
        return "{$column} IN ({$in})";
    }
}
