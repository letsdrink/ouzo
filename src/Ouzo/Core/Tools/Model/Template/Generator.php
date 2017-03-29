<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Model\Template;

use Exception;
use Ouzo\AutoloadNamespaces;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Inflector;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class Generator
{
    private $tableName;
    private $className;
    private $adapter;
    private $dialectShortName;
    private $tablePrefix;
    private $namespace;
    private $shortArrays;

    public function __construct($tableName, $className = null, $nameSpace = '', $tablePrefix = 't', $shortArrays = false)
    {
        $this->tableName = $tableName;
        $this->className = $className;
        $this->namespace = $this->normalizeNameSpace($nameSpace);
        $this->tablePrefix = $tablePrefix;
        $this->adapter = $this->dialectAdapter();
        $this->dialectShortName = $this->getDialectShortName($this->adapter);
        $this->shortArrays = $shortArrays;
    }

    private function normalizeNameSpace($nameSpace)
    {
        return str_replace('/', '\\', $nameSpace);
    }

    private function getDialectShortName($adapterObject)
    {
        return mb_strtolower(str_replace('Dialect', '', $this->objectShortClassName($adapterObject)));
    }

    private function thisNamespace()
    {
        $thisReflection = new ReflectionClass($this);
        return $thisReflection->getNamespaceName();
    }

    private function objectShortClassName($object)
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
        $dialectShortName = $this->objectShortClassName($dialect);
        $selfClassPath = $this->thisNamespace();
        $generatorDialect = "$selfClassPath\\Dialect\\$dialectShortName";
        if (!class_exists($generatorDialect)) {
            throw new GeneratorException("Model generator for '$dialectShortName' does not exists.");
        }
        return new $generatorDialect($this->tableName);
    }

    private function removeTablePrefix($tableNameParts)
    {
        if (Arrays::first($tableNameParts) == $this->tablePrefix) {
            array_shift($tableNameParts);
        }
        return $tableNameParts;
    }

    public function getTemplateClassName()
    {
        return $this->className ?: $this->classNameFromTableName();
    }

    public function templateContents()
    {
        $tableInfo = new TableInfo($this->adapter);
        $stubReplacer = new ClassStubPlaceholderReplacer($this->getTemplateClassName(), $tableInfo, $this->getClassNamespace(), $this->shortArrays);
        return $stubReplacer->contents();
    }

    public function saveToFile($fileName)
    {
        if (is_file($fileName)) {
            throw new GeneratorException("File already exists '$fileName'.");
        }
        $this->preparePaths(dirname($fileName));
        file_put_contents($fileName, $this->templateContents());
    }

    public function getClassNamespace()
    {
        $parts = explode('\\', $this->namespace);
        $parts = Arrays::map($parts, 'ucfirst');
        $modelNamespace = trim(AutoloadNamespaces::getModelNamespace(), '\\');
        if (!Strings::startsWith($this->namespace, $modelNamespace)) {
            $parts = array_merge([$modelNamespace], $parts);
        }
        $parts = Arrays::filterNotBlank($parts);
        return implode('\\', $parts);
    }

    private function classNameFromTableName()
    {
        $parts = explode('_', $this->tableName);
        $parts = $this->removeTablePrefix($parts);
        $parts[] = Inflector::singularize(array_pop($parts));
        $parts = Arrays::map($parts, 'ucfirst');
        return implode('', $parts);
    }

    private function preparePaths($basename)
    {
        if (!is_dir($basename)) {
            mkdir($basename, 0777, true);
        }
    }
}

class GeneratorException extends Exception
{
}
