<?php
namespace Ouzo\Tools\Model\Template\Dialect;

use Ouzo\Db;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Utilities\Arrays;

class MySqlDialect extends Dialect
{
    public function primaryKey()
    {
        return $this->_getPrimaryKey($this->tableName());
    }

    public function columns()
    {
        return array_values($this->_getTableColumns($this->tableName()));
    }

    private function _getPrimaryKey($tableName)
    {
        $primaryKey = Db::getInstance()->query("SHOW KEYS FROM $tableName WHERE Key_name = 'PRIMARY'")->fetch();
        if ($primaryKey)
            return Arrays::getValue($primaryKey, 'Column_name');
        else
            return '';
    }

    private function _getTableColumns($tableName)
    {
        $schema = Db::getInstance()->query("SHOW COLUMNS FROM $tableName")->fetchAll();
        $tableColumns = array();
        foreach ($schema as $columnInfo) {
            $columnName = $columnInfo['Field'];
            $columnDefault = $columnInfo['Default'];
            $columnType = $this->dataTypeToPhpType($columnInfo['Type']);
            $tableColumns[$columnName] = new DatabaseColumn($columnName, $columnType, $columnDefault);
        }
        return $tableColumns;
    }

    public function dataTypeToPhpType($dataType)
    {
        $dataType = mb_strtolower($dataType);
        if (preg_match('/int/', $dataType))
            return 'int';
        if (preg_match('/double.*|float.*|decimal.*/', $dataType))
            return 'float';
        switch ($dataType) {
            case 'bool':
                return 'bool';
            case 'boolean':
                return 'bool';
            default:
                return 'string';
        }
    }
}