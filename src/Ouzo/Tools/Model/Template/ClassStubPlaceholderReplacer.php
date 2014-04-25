<?php
namespace Ouzo\Tools\Model\Template;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class ClassStubPlaceholderReplacer
{
    /**
     * @var TableInfo
     */
    private $tableInfo;
    private $className;
    private $classStub;

    function __construct($className, $tableInfo)
    {
        $this->className = $className;
        $this->tableInfo = $tableInfo;
        $this->classStub = new ClassStub();
    }

    private function _setupTablePlaceholderReplacements()
    {
        $this->_setupTableNameReplacement();
        $this->_setupPrimaryKeyReplacement();
        $this->_setupSequenceReplacement();
    }

    public function contents()
    {
        $this->_setupTablePlaceholderReplacements();
        $this->classStub->addPlaceholderReplacement('class', $this->className);
        Arrays::map($this->tableInfo->tableColumns, array($this->classStub, 'addColumn'));
        return $this->classStub->contents();
    }

    private function _setupTableNameReplacement()
    {
        $tableName = $this->tableInfo->tableName;
        $defaultTableName = Strings::tableize($this->className);
        $placeholderTableName = ($tableName != $defaultTableName) ? $tableName : '';
        $this->_addTableSetupItem('table', $placeholderTableName);
    }

    private function _setupPrimaryKeyReplacement()
    {
        $primaryKey = $this->tableInfo->primaryKeyName;
        $placeholderPrimaryKey = ($primaryKey != 'id') ? $primaryKey : '';
        $this->_addTableSetupItem('primaryKey', $placeholderPrimaryKey);
    }

    private function _setupSequenceReplacement()
    {
        $sequenceName = $this->tableInfo->sequenceName;
        $defaultSequenceName = $this->tableInfo->tableName . '_' . $this->tableInfo->primaryKeyName . '_seq';
        $placeholderSequenceName = ($sequenceName != $defaultSequenceName) ? $sequenceName : '';
        $this->_addTableSetupItem('sequence', $placeholderSequenceName);
    }

    private function _addTableSetupItem($name, $value)
    {
        if ($value) {
            $value = sprintf("'%s' => '%s',", $name, $value);
        }
        $this->classStub->addPlaceholderReplacement("table_$name", $value);
    }
}