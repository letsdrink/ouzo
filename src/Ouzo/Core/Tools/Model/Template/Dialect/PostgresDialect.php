<?php
namespace Ouzo\Tools\Model\Template\Dialect;

use Ouzo\Db;
use Ouzo\Tools\Model\Template\DatabaseColumn;
use Ouzo\Utilities\Arrays;

class PostgresDialect extends Dialect
{
    public function primaryKey()
    {
        return $this->_getPrimaryKey($this->tableName());
    }

    public function sequence()
    {
        $tableColumns = $this->_getTableColumns($this->tableName());
        return $this->getSequenceName($tableColumns, $this->primaryKey());
    }

    public function columns()
    {
        return array_values($this->_getTableColumns($this->tableName()));
    }

    public function getSequenceName($tableColumns, $primaryKey)
    {
        $primaryColumnInfo = Arrays::getValue($tableColumns, $primaryKey);
        if (!$primaryColumnInfo || empty($primaryColumnInfo->default)) {
            return '';
        }
        preg_match("/nextval\('(?<sequence>.*)'.*\)/", strtolower($primaryColumnInfo->default), $matches);
        return Arrays::getValue($matches, 'sequence');
    }

    private function _getPrimaryKey($tableName)
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
        } else {
            return '';
        }
    }

    private function _getTableColumns($tableName)
    {
        $schema = Db::getInstance()
            ->query("SELECT column_name, data_type, column_default FROM information_schema.columns WHERE table_name = '$tableName' ORDER BY ordinal_position")
            ->fetchAll();
        $tableColumns = array();
        foreach ($schema as $columnInfo) {
            $columnName = $columnInfo['column_name'];
            $columnDefault = $columnInfo['column_default'];
            $columnType = $this->_postgresDataTypeToPhpType($columnInfo['data_type']);
            $tableColumns[$columnName] = new DatabaseColumn($columnName, $columnType, $columnDefault);
        }
        return $tableColumns;
    }

    private function _postgresDataTypeToPhpType($dataType)
    {
        switch ($dataType) {
            case 'boolean':
                return 'bool';
            case 'integer':
                return 'int';
            default:
                return 'string';
        }
    }
}
