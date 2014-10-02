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
    private $classNamespace;

    public function __construct($className, $tableInfo, $classNamespace = '')
    {
        $this->className = $className;
        $this->tableInfo = $tableInfo;
        $this->classNamespace = $classNamespace;
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
        $this->classStub->addPlaceholderReplacement('namespace', $this->classNamespace);
        Arrays::map($this->tableInfo->tableColumns, array($this->classStub, 'addColumn'));
        return $this->classStub->contents();
    }

    private function _setupTableNameReplacement()
    {
        $tableName = $this->tableInfo->tableName;
        $defaultTableName = Strings::tableize($this->className);
        $placeholderTableName = ($tableName != $defaultTableName) ? $tableName : '';
        $this->classStub->addTableSetupItem('table', $placeholderTableName);
    }

    private function _setupPrimaryKeyReplacement()
    {
        $primaryKey = $this->tableInfo->primaryKeyName;
        $this->classStub->addTablePrimaryKey($primaryKey);
    }

    private function _setupSequenceReplacement()
    {
        $sequenceName = $this->tableInfo->sequenceName;
        $defaultSequenceName = $this->tableInfo->tableName . '_' . $this->tableInfo->primaryKeyName . '_seq';
        $placeholderSequenceName = ($sequenceName != $defaultSequenceName) ? $sequenceName : '';
        $this->classStub->addTableSetupItem('sequence', $placeholderSequenceName);
    }
}