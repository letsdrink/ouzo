<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Restriction\Restriction;
use Ouzo\Utilities\Objects;

class QueryBoundValuesExtractor
{
    private array $boundValues = [];
    private Query $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function extract(): array
    {
        $this->addBindValues($this->query);
        return $this->boundValues;
    }

    private function addBindValues(Query $query): void
    {
        if ($query->table instanceof Query) {
            $this->addBindValues($query->table);
        }
        $this->addBindValue(array_values($query->updateAttributes));

        $this->addBindValuesFromJoinClauses($query->joinClauses);

        foreach ($query->whereClauses as $whereClause) {
            $this->addBindValuesFromWhereClause($whereClause);
        }

        if ($query->type == QueryType::$UPSERT) {
            $this->addBindValue(array_values($query->updateAttributes));
        }

        if ($query->limit !== null) {
            $this->addBindValue($query->limit);
        }
        if ($query->offset) {
            $this->addBindValue($query->offset);
        }
    }

    /** @param JoinClause[] $joinClauses */
    private function addBindValuesFromJoinClauses(array $joinClauses): void
    {
        foreach ($joinClauses as $joinClause) {
            foreach ($joinClause->onClauses as $onClause) {
                $this->addBindValuesFromWhereClause($onClause);
            }
        }
    }

    /** @param WhereClause $whereClause */
    private function addBindValuesFromWhereClause(WhereClause $whereClause): void
    {
        if (!$whereClause->isEmpty()) {
            $this->addBindValue($whereClause->getParameters());
        }
    }

    private function addBindArrayValue(array $array): void
    {
        foreach ($array as $value) {
            if ($value instanceof Restriction) {
                $this->boundValues = array_merge($this->boundValues, $value->getValues());
            } else {
                $this->boundValues[] = $value;
            }
        }
    }

    public function addBindValue(mixed $value): void
    {
        if (is_array($value)) {
            $this->addBindArrayValue($value);
        } else {
            $this->boundValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }
}
