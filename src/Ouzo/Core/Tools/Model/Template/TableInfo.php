<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template;

use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;

class TableInfo
{
    public string $tableName;
    public string $primaryKeyName;
    public string $sequenceName;
    /** @var string[] */
    public array $tableColumns;

    public function __construct(Dialect $dialect)
    {
        $this->tableName = $dialect->tableName();
        $this->primaryKeyName = $dialect->primaryKey();
        $this->sequenceName = $dialect->sequence();
        $this->tableColumns = $this->getColumnsWithoutPrimary($dialect);
    }

    private function getColumnsWithoutPrimary(Dialect $dialect): array
    {
        $primaryKeyName = $this->primaryKeyName;
        $columns = $dialect->columns();
        if ($primaryKeyName != 'id') {
            return Arrays::filter($columns, fn(DatabaseColumn $column) => $column->name != $primaryKeyName);
        }
        return $columns;
    }
}
