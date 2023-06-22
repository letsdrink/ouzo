<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\Dialect;

use Ouzo\Db\OnConflict;
use Ouzo\Db\Query;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;

class PostgresDialect extends Dialect
{
    public function getConnectionErrorCodes(): array
    {
        return ['57000', '57014', '57P01', '57P02', '57P03'];
    }

    public function getErrorCode(array $errorInfo): mixed
    {
        return Arrays::getValue($errorInfo, 0);
    }

    public function batchInsert(string $table, string $primaryKey, $columns, $batchSize, ?OnConflict $onConflict): string
    {
        $valueClause = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $valueClauses = implode(', ', array_fill(0, $batchSize, $valueClause));
        $joinedColumns = implode(', ', $columns);
        $sql = "INSERT INTO {$table} ($joinedColumns) VALUES $valueClauses";

        if ($onConflict) {
            if ($onConflict->isDoNothingAction()) {
                $sql .= $this->onConflictDoNothing();
            }
            if ($onConflict->isUpdateAction()) {
                $query = new Query();
                $query->updateAttributes = $onConflict->getOnConflictUpdateValues();
                $query->upsertConflictColumns = $onConflict->getOnConflictColumns();
                $this->query = $query;
                $sql .= $this->onConflictUpdate();
            }
        }

        if ($primaryKey) {
            return "{$sql} RETURNING {$primaryKey}";
        }
        return $sql;
    }

    protected function insertEmptyRow(): string
    {
        return "INSERT INTO {$this->query->table} DEFAULT VALUES";
    }

    public function regexpMatcher(): string
    {
        return '~';
    }

    protected function quote(string $word): string
    {
        return "\"{$word}\"";
    }

    public function onConflictUpdate(): string
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        $upsertConflictColumns = $this->query->upsertConflictColumns;
        $joinedColumns = implode(', ', $upsertConflictColumns);
        return " ON CONFLICT ({$joinedColumns}) DO UPDATE SET {$attributes}";
    }

    public function onConflictDoNothing(): string
    {
        return " ON CONFLICT DO NOTHING";
    }

    protected function getDistinctOnQuery(): string
    {
        return 'DISTINCT ON (' . Joiner::on(', ')->join($this->query->distinctOnColumns) . ') ';
    }

    protected function fromForDistinctCount(): string
    {
        $alias = $this->query->aliasTable ?: $this->query->table;
        $distinct = $this->getDistinctOnQuery();
        return " FROM (SELECT {$distinct}* FROM {$this->tableOrSubQuery()}) AS {$alias}";
    }
}
