<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

use Closure;
use InvalidArgumentException;
use Ouzo\Db\ModelQueryBuilder;

abstract class WhereClause
{
    abstract public function isEmpty(): bool;

    abstract public function toSql(): string;

    abstract public function getParameters(): array;

    public function isNeverSatisfied(): bool
    {
        return false;
    }

    public static function isNotEmptyFunction(): Closure
    {
        return fn(WhereClause $whereClause) => !$whereClause->isEmpty();
    }

    public static function create(array|string|WhereClause|null $where, null|string|array $parameters = []): WhereClause
    {
        if (is_null($where)) {
            return new EmptyWhereClause();
        }
        if ($where instanceof WhereClause) {
            return $where;
        }
        if (is_array($where)) {
            return new ArrayWhereClause($where, 'AND');
        }
        if (is_string($where)) {
            return new SqlWhereClause($where, $parameters);
        }
        throw new InvalidArgumentException("Cannot create a WhereClause for given arguments");
    }

    public static function exists(ModelQueryBuilder $queryBuilder): ExistsClause
    {
        return new ExistsClause($queryBuilder->getQuery());
    }

    public static function notExists(ModelQueryBuilder $queryBuilder): ExistsClause
    {
        return new ExistsClause($queryBuilder->getQuery(), true);
    }
}
