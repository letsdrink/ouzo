<?php
namespace Ouzo\Tools\Model\Template;

use Exception;
use Ouzo\Config;
use Ouzo\Db;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Inflector;
use ReflectionClass;

class Generator
{
    private $_tableName;
    private $_className;
    private $_adapter;
    private $_dialectShortName;
    private $_tablePrefix;

    function __construct($tableName, $className = null, $tablePrefix = 't')
    {
        $this->_tableName = $tableName;
        $this->_className = $className;
        $this->_tablePrefix = $tablePrefix;
        $this->_adapter = $this->dialectAdapter();
        $this->_dialectShortName = $this->_getDialectShortName($this->_adapter);
    }

    private function _getDialectShortName($adapterObject)
    {
        return mb_strtolower(str_replace('Dialect', '', $this->_objectShortClassName($adapterObject)));
    }

    private function _thisNamespace()
    {
        $thisReflection = new ReflectionClass($this);
        return $thisReflection->getNamespaceName();
    }

    private function _objectShortClassName($object)
    {
        $objectReflection = new ReflectionClass($object);
        return $objectReflection->getShortName();
    }

    /**
     * @return Dialect
     */
    public function dialectAdapter()
    {
        $dialect = DialectFactory::create();
        $dialectShortName = $this->_objectShortClassName($dialect);
        $selfClassPath = $this->_thisNamespace($this);
        $generatorDialect = "$selfClassPath\\Dialect\\$dialectShortName";
        if (!class_exists($generatorDialect)) {
            throw new GeneratorException("Model generator for '$dialectShortName' does not exists.");
        }
        return new $generatorDialect($this->_tableName);
    }

    private function _removeTablePrefix($tableNameParts)
    {
        if (Arrays::first($tableNameParts) == $this->_tablePrefix) {
            array_shift($tableNameParts);
        }
        return $tableNameParts;
    }

    public function getTemplateClassName()
    {
        $parts = explode('_', $this->_tableName);
        $parts = $this->_removeTablePrefix($parts);
        $parts[] = Inflector::singularize(array_pop($parts));
        $parts = Arrays::map($parts, 'ucfirst');
        return implode('', $parts);
    }

    public function templateContents()
    {
        $tableInfo = new TableInfo($this->_adapter);
        $stubReplacer = new ClassStubPlaceholderReplacer($this->getTemplateClassName(), $tableInfo);
        return $stubReplacer->contents();
    }

    public function saveToFile($fileName)
    {
        if (is_file($fileName)) {
            throw new GeneratorException("File already exists '$fileName'.");
        }
        file_put_contents($fileName, $this->templateContents());
    }
}

class GeneratorException extends Exception
{
}