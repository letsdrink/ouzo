<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;

use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\Restriction\Restriction;
use Ouzo\Utilities\Objects;

class QueryBoundValuesExtractor
{
    /** @var array */
    private $boundValues = [];
    /** @var Query */
    private $query;

    /**
     * @param Query $query
     */
    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function extract()
    {
        $this->addBindValues($this->query);
        return $this->boundValues;
    }

    /**
     * @param $query
     * @return void
     */
    private function addBindValues($query)
    {
        if ($query->table instanceof Query) {
            $this->addBindValues($query->table);
        }
        $this->addBindValue(array_values($query->updateAttributes));

        $this->addBindValuesFromJoinClauses($query->joinClauses);

        foreach ($query->whereClauses as $whereClause) {
            $this->addBindValuesFromWhereClause($whereClause);
        }
        if ($query->limit !== null) {
            $this->addBindValue($query->limit);
        }
        if ($query->offset) {
            $this->addBindValue($query->offset);
        }
    }

    /**
     * @param JoinClause[] $joinClauses
     * @return void
     */
    private function addBindValuesFromJoinClauses($joinClauses)
    {
        foreach ($joinClauses as $joinClause) {
            foreach ($joinClause->onClauses as $onClause) {
                $this->addBindValuesFromWhereClause($onClause);
            }
        }
    }

    /**
     * @param WhereClause $whereClause
     * @return void
     */
    private function addBindValuesFromWhereClause(WhereClause $whereClause)
    {
        if (!$whereClause->isEmpty()) {
            $this->addBindValue($whereClause->getParameters());
        }
    }

    /**
     * @param array $array
     * @return void
     */
    private function addBindArrayValue(array $array)
    {
        foreach ($array as $value) {
            if ($value instanceof Restriction) {
                $this->boundValues = array_merge($this->boundValues, $value->getValues());
            } else {
                $this->boundValues[] = $value;
            }
        }
    }

    /**
     * @param mixed $value
     */
    public function addBindValue($value)
    {
        if (is_array($value)) {
            $this->addBindArrayValue($value);
        } else {
            $this->boundValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }
}
