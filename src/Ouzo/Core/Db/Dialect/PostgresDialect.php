<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\Dialect;

use Ouzo\Utilities\Arrays;

class PostgresDialect extends Dialect
{
    /**
     * @inheritdoc
     */
    public function getConnectionErrorCodes()
    {
        return ['57000', '57014', '57P01', '57P02', '57P03'];
    }

    /**
     * @inheritdoc
     */
    public function getErrorCode($errorInfo)
    {
        return Arrays::getValue($errorInfo, 0);
    }

    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    protected function insertEmptyRow()
    {
        return "INSERT INTO {$this->query->table} DEFAULT VALUES";
    }

    /**
     * @inheritdoc
     */
    public function regexpMatcher()
    {
        return '~';
    }

    /**
     * @inheritdoc
     */
    protected function quote($word)
    {
        return '"' . $word . '"';
    }

    /**
     * @inheritdoc
     */
    public function onConflictUpdate()
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        $upsertConflictColumns = $this->query->upsertConflictColumns;
        $joinedColumns = implode(', ', $upsertConflictColumns);
        return " ON CONFLICT ({$joinedColumns}) DO UPDATE SET {$attributes}";
    }

    /**
     * @inheritdoc
     */
    public function onConflictDoNothing()
    {
        return " ON CONFLICT DO NOTHING";
    }
}
