<?php
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
    function __construct($dialect)
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