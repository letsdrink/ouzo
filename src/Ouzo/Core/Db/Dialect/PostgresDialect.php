<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use Ouzo\Utilities\Arrays;

class PostgresDialect extends Dialect
{
    public function getConnectionErrorCodes()
    {
        return array('57000', '57014', '57P01', '57P02', '57P03');
    }

    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0);
    }

    public function batchInsert($table, $primaryKey, $columns, $batchSize)
    {
        $valueClause = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $valueClauses = implode(', ', array_fill(0, $batchSize, $valueClause));
        $joinedColumns = implode(', ', $columns);
        $sql = "INSERT INTO {$table} ($joinedColumns) VALUES $valueClauses";
        if ($primaryKey) {
            $sql .= ' RETURNING ' . $primaryKey;
        }
        return $sql;
    }

    protected function insertEmptyRow()
    {
        return "INSERT INTO {$this->_query->table} DEFAULT VALUES";
    }

    public function regexpMatcher()
    {
        return '~';
    }
}
