<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db;


use Ouzo\Restriction\Restriction;
use Ouzo\Utilities\Objects;

class QueryBoundValuesExtractor
{
    private $_boundValues = array();
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function extract()
    {
        $this->_addBindValues($this->query);
        return $this->_boundValues;
    }

    private function _addBindValues($query)
    {
        if ($query->table instanceof Query) {
            $this->_addBindValues($query->table);
        }
        $this->_addBindValue(array_values($query->updateAttributes));

        $this->_addBindValuesFromJoinClauses($query->joinClauses);

        foreach ($query->whereClauses as $whereClause) {
            $this->_addBindValuesFromWhereClause($whereClause);
        }
        if ($query->limit) {
            $this->_addBindValue($query->limit);
        }
        if ($query->offset) {
            $this->_addBindValue($query->offset);
        }
    }

    private function _addBindValuesFromJoinClauses($joinClauses)
    {
        foreach ($joinClauses as $joinClause) {
            foreach ($joinClause->onClauses as $onClause) {
                $this->_addBindValuesFromWhereClause($onClause);
            }
        }
    }

    private function _addBindValuesFromWhereClause($whereClause)
    {
        if (!$whereClause->isEmpty()) {
            $this->_addBindValue($whereClause->getParameters());
        }
    }

    private function _addBindArrayValue(array $array)
    {
        foreach ($array as $value) {
            if ($value instanceof Restriction) {
                $this->_boundValues = array_merge($this->_boundValues, $value->getValues());
            } else {
                $this->_boundValues[] = $value;
            }
        }
    }

    public function _addBindValue($value)
    {
        if (is_array($value)) {
            $this->_addBindArrayValue($value);
        } else {
            $this->_boundValues[] = is_bool($value) ? Objects::booleanToString($value) : $value;
        }
    }
}