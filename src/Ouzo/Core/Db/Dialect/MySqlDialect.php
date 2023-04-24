<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\Dialect;

use InvalidArgumentException;
use Ouzo\Db\OnConflict;
use Ouzo\Db\QueryType;
use Ouzo\Utilities\Arrays;

class MySqlDialect extends Dialect
{
    public function table(): string
    {
        $alias = $this->query->aliasTable;
        $table = $this->tableOrSubQuery();
        if ($alias) {
            $aliasOperator = $this->query->type == QueryType::$DELETE ? '' : ' AS ';
            return $table . $aliasOperator . $alias;
        }
        return $table;
    }

    public function getConnectionErrorCodes(): array
    {
        return [2003, 2006];
    }

    public function getErrorCode(array $errorInfo): mixed
    {
        return Arrays::getValue($errorInfo, 1);
    }

    public function using(): string
    {
        return $this->usingClause($this->query->usingClauses, ' INNER JOIN ', $this->query->table, $this->query->aliasTable);
    }

    public function batchInsert(string $table, string $primaryKey, array $columns, int $batchSize, ?OnConflict $onConflict): string
    {
        throw new InvalidArgumentException("Batch insert not supported in mysql");
    }

    protected function insertEmptyRow(): string
    {
        return "INSERT INTO {$this->query->table} VALUES ()";
    }

    public function regexpMatcher(): string
    {
        return 'REGEXP';
    }

    protected function quote(string $word): string
    {
        return "`{$word}`";
    }

    public function onConflictUpdate(): string
    {
        $attributes = DialectUtil::buildAttributesPartForUpdate($this->query->updateAttributes);
        return " ON DUPLICATE KEY UPDATE {$attributes}";
    }

    public function onConflictDoNothing(): string
    {
        throw new InvalidArgumentException("On conflict do nothing is not supported in mysql");
    }

    protected function getDistinctOnQuery(): string
    {
        throw new InvalidArgumentException("DISTINCT ON is not supported in mysql");
    }
}
