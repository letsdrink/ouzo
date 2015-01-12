<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Model\Template;

use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;

class TableInfo
{
    public $tableName;
    public $primaryKeyName;
    public $sequenceName;
    public $tableColumns;

    /**
     * @param Dialect $dialect
     */
    public function __construct($dialect)
    {
        $this->tableName = $dialect->tableName();
        $this->primaryKeyName = $dialect->primaryKey();
        $this->sequenceName = $dialect->sequence();
        $this->tableColumns = $this->_getColumnsWithoutPrimary($dialect);
    }

    private function _getColumnsWithoutPrimary(Dialect $dialect)
    {
        $primaryKeyName = $this->primaryKeyName;
        return Arrays::filter($dialect->columns(), function (DatabaseColumn $column) use ($primaryKeyName) {
            return ($column->name != $primaryKeyName);
        });
    }
}
