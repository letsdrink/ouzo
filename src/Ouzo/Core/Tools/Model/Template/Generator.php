<?php
namespace Ouzo\Tools\Model\Template;

use Exception;
use Ouzo\AutoloadNamespaces;
use Ouzo\Config;
use Ouzo\Db;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Inflector;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class Generator
{
    private $_tableName;
    private $_className;
    private $_adapter;
    private $_dialectShortName;
    private $_tablePrefix;
    private $_nameSpace;
    private $_shortArrays;

    public function __construct($tableName, $className = null, $nameSpace = '', $tablePrefix = 't', $shortArrays = false)
    {
        $this->_tableName = $tableName;
        $this->_className = $className;
        $this->_nameSpace = $this->_normalizeNameSpace($nameSpace);
        $this->_tablePrefix = $tablePrefix;
        $this->_adapter = $this->dialectAdapter();
        $this->_dialectShortName = $this->_getDialectShortName($this->_adapter);
        $this->_shortArrays = $shortArrays;
    }

    private function _normalizeNameSpace($nameSpace)
    {
        return str_replace('/', '\\', $nameSpace);
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
     * @throws GeneratorException
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
        if ($this->_className) {
            return $this->_className;
        } else {
            return $this->_classNameFromTableName();
        }
    }

    public function templateContents()
    {
        $tableInfo = new TableInfo($this->_adapter);
        $stubReplacer = new ClassStubPlaceholderReplacer($this->getTemplateClassName(), $tableInfo, $this->getClassNamespace(), $this->_shortArrays);
        return $stubReplacer->contents();
    }

    public function saveToFile($fileName)
    {
        if (is_file($fileName)) {
            throw new GeneratorException("File already exists '$fileName'.");
        }
        $this->_preparePaths(dirname($fileName));
        file_put_contents($fileName, $this->templateContents());
    }

    public function getClassNamespace()
    {
        $parts = explode('\\', $this->_nameSpace);
        $parts = Arrays::map($parts, 'ucfirst');
        $modelNamespace = rtrim(AutoloadNamespaces::getModelNamespace(), '\\');
        if (!Strings::startsWith($this->_nameSpace, $modelNamespace)) {
            $parts = array_merge(array($modelNamespace), $parts);
        }
        return implode('\\', $parts);
    }

    private function _classNameFromTableName()
    {
        $parts = explode('_', $this->_tableName);
        $parts = $this->_removeTablePrefix($parts);
        $parts[] = Inflector::singularize(array_pop($parts));
        $parts = Arrays::map($parts, 'ucfirst');
        return implode('', $parts);
    }

    private function _preparePaths($basename)
    {
        if (!is_dir($basename)) {
            mkdir($basename, 0777, true);
        }
    }
}

class GeneratorException extends Exception
{
}
