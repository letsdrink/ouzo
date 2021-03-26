<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template\Dialect;

use Ouzo\Db;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Utilities\Arrays;

class MySqlDialect extends Dialect
{
    public function primaryKey(): string
    {
        return $this->getPrimaryKey($this->tableName());
    }

    public function columns(): array
    {
        return array_values($this->getTableColumns($this->tableName()));
    }

    private function getPrimaryKey(string $tableName): string
    {
        $primaryKey = Db::getInstance()->query("SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'")->fetch();
        if ($primaryKey) {
            return Arrays::getValue($primaryKey, 'Column_name');
        }
        return '';
    }

    /** @return string[] */
    private function getTableColumns(string $tableName): array
    {
        $schema = Db::getInstance()->query("SHOW COLUMNS FROM $tableName")->fetchAll();
        $tableColumns = [];
        foreach ($schema as $columnInfo) {
            $columnName = $columnInfo['Field'];
            $columnDefault = $columnInfo['Default'];
            $columnType = $this->dataTypeToPhpType($columnInfo['Type']);
            $tableColumns[$columnName] = new DatabaseColumn($columnName, $columnType, $columnDefault);
        }
        return $tableColumns;
    }

    public function dataTypeToPhpType(string $dataType): string
    {
        $dataType = mb_strtolower($dataType);
        if (mb_strpos($dataType, 'int') !== false) {
            return 'int';
        }
        if (preg_match('/double.*|float.*|decimal.*/', $dataType)) {
            return 'float';
        }
        return match ($dataType) {
            'bool', 'boolean' => 'bool',
            default => 'string'
        };
    }
}
