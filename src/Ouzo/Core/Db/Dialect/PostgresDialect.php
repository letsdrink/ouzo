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
    #[Override]
    public function getConnectionErrorCodes(): array
    {
        return ['57000', '57014', '57P01', '57P02', '57P03'];
    }

    #[Override]
    public function getErrorCode(array $errorInfo): mixed
    {
        return Arrays::getValue($errorInfo, 0);
    }

    #[Override]
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

    #[Override]
    protected function insertEmptyRow(): string
    {
        return "INSERT INTO {$this->query->table} DEFAULT VALUES";
    }

    #[Override]
    public function regexpMatcher(): string
    {
        return '~';
    }

    #[Override]
    protected function quote(string $word): string
    {
        return "\"{$word}\"";
    }

    #[Override]
    public function onConflictUpdate(): string
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        $upsertConflictColumns = $this->query->upsertConflictColumns;
        $joinedColumns = implode(', ', $upsertConflictColumns);
        return " ON CONFLICT ({$joinedColumns}) DO UPDATE SET {$attributes}";
    }

    #[Override]
    public function onConflictDoNothing(): string
    {
        return " ON CONFLICT DO NOTHING";
    }

    #[Override]
    protected function getDistinctOnQuery(): string
    {
        return 'DISTINCT ON (' . Joiner::on(', ')->join($this->query->distinctOnColumns) . ') ';
    }

    #[Override]
    protected function wrapQueryWithDistinctCount(string $sql): string
    {
        return "SELECT count(*) FROM ({$sql}) AS count_data";
    }
}
