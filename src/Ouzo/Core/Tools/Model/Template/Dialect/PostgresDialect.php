<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template\Dialect;

use Ouzo\Db;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Utilities\Arrays;

class PostgresDialect extends Dialect
{
    public function primaryKey(): string
    {
        return $this->getPrimaryKey($this->tableName());
    }

    public function sequence(): string
    {
        $tableColumns = $this->getTableColumns($this->tableName());
        return $this->getSequenceName($tableColumns, $this->primaryKey());
    }

    public function columns(): array
    {
        return array_values($this->getTableColumns($this->tableName()));
    }

    public function getSequenceName(array $tableColumns, string $primaryKey): string
    {
        $primaryColumnInfo = Arrays::getValue($tableColumns, $primaryKey);
        if (!$primaryColumnInfo || empty($primaryColumnInfo->default)) {
            return '';
        }
        preg_match("/nextval\('(?<sequence>.*)'.*\)/", strtolower($primaryColumnInfo->default), $matches);
        return Arrays::getValue($matches, 'sequence');
    }

    private function getPrimaryKey(string $tableName): string
    {
        $primaryKey = Db::getInstance()->query(
            "SELECT pg_attribute.attname
         FROM pg_index, pg_class, pg_attribute
         WHERE
            pg_class.oid = '$tableName'::REGCLASS AND
            indrelid = pg_class.oid AND
            pg_attribute.attrelid = pg_class.oid AND
            pg_attribute.attnum = ANY(pg_index.indkey)
            AND indisprimary;
        ")->fetch();
        if ($primaryKey) {
            return Arrays::getValue($primaryKey, 'attname');
        }
        return '';
    }

    private function getTableColumns(string $tableName): array
    {
        $schema = Db::getInstance()
            ->query("SELECT column_name, data_type, column_default FROM information_schema.columns WHERE table_name = '$tableName' ORDER BY ordinal_position")
            ->fetchAll();
        $tableColumns = [];
        foreach ($schema as $columnInfo) {
            $columnName = $columnInfo['column_name'];
            $columnDefault = $columnInfo['column_default'];
            $columnType = $this->postgresDataTypeToPhpType($columnInfo['data_type']);
            $tableColumns[$columnName] = new DatabaseColumn($columnName, $columnType, $columnDefault);
        }
        return $tableColumns;
    }

    private function postgresDataTypeToPhpType(string $dataType): string
    {
        return match ($dataType) {
            'boolean' => 'bool',
            'integer' => 'int',
            default => 'string'
        };
    }
}
