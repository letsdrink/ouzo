<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class ClassStubPlaceholderReplacer
{
    private ClassStub $classStub;

    public function __construct(
        private string $className,
        private TableInfo $tableInfo,
        private string $classNamespace = ''
    )
    {
        $this->classStub = new ClassStub();
    }

    private function setupTablePlaceholderReplacements(): void
    {
        $this->setupTableNameReplacement();
        $this->setupPrimaryKeyReplacement();
        $this->setupSequenceReplacement();
    }

    public function contents(): string
    {
        $this->setupTablePlaceholderReplacements();
        $this->classStub->addPlaceholderReplacement('class', $this->className);
        $this->classStub->addPlaceholderReplacement('namespace', $this->classNamespace);
        Arrays::map($this->tableInfo->tableColumns, fn($column) => $this->classStub->addColumn($column));
        return $this->classStub->contents();
    }

    private function setupTableNameReplacement(): void
    {
        $tableName = $this->tableInfo->tableName;
        $defaultTableName = Strings::tableize($this->className);
        $placeholderTableName = ($tableName != $defaultTableName) ? $tableName : '';
        $this->classStub->addTableSetupItem('table', $placeholderTableName);
    }

    private function setupPrimaryKeyReplacement(): void
    {
        $primaryKey = $this->tableInfo->primaryKeyName;
        $this->classStub->addTablePrimaryKey($primaryKey);
    }

    private function setupSequenceReplacement(): void
    {
        $sequenceName = $this->tableInfo->sequenceName;
        $defaultSequenceName = "{$this->tableInfo->tableName}_{$this->tableInfo->primaryKeyName}_seq";
        $placeholderSequenceName = $sequenceName != $defaultSequenceName ? $sequenceName : '';
        $this->classStub->addTableSetupItem('sequence', $placeholderSequenceName);
    }
}
