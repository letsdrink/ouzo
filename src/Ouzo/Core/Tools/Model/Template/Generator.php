<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tools\Model\Template;

use Ouzo\AutoloadNamespaces;
use Ouzo\Db\Dialect\DialectFactory;
use Ouzo\Tools\Model\Template\Dialect\Dialect;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Inflector;
use Ouzo\Utilities\Strings;
use ReflectionClass;

class Generator
{
    private $adapter;
    private $dialectShortName;
    private string $namespace;

    public function __construct(
        private string $tableName,
        private ?string $className = null,
        private string $nameSpace = '',
        private string $tablePrefix = 't'
    )
    {
        $this->namespace = $this->normalizeNameSpace($nameSpace);
        $this->adapter = $this->dialectAdapter();
        $this->dialectShortName = $this->getDialectShortName($this->adapter);
    }

    private function normalizeNameSpace(string $nameSpace): string
    {
        return str_replace('/', '\\', $nameSpace);
    }

    private function getDialectShortName(object $adapterObject): string
    {
        return mb_strtolower(str_replace('Dialect', '', $this->objectShortClassName($adapterObject)));
    }

    private function thisNamespace(): string
    {
        $thisReflection = new ReflectionClass($this);
        return $thisReflection->getNamespaceName();
    }

    private function objectShortClassName(object $object): string
    {
        $objectReflection = new ReflectionClass($object);
        return $objectReflection->getShortName();
    }

    public function dialectAdapter(): Dialect
    {
        $dialect = DialectFactory::create();
        $dialectShortName = $this->objectShortClassName($dialect);
        $selfClassPath = $this->thisNamespace();
        $generatorDialect = "{$selfClassPath}\\Dialect\\{$dialectShortName}";
        if (!class_exists($generatorDialect)) {
            throw new GeneratorException("Model generator for '{$dialectShortName}' does not exists.");
        }
        return new $generatorDialect($this->tableName);
    }

    private function removeTablePrefix(array $tableNameParts): array
    {
        if (Arrays::first($tableNameParts) == $this->tablePrefix) {
            array_shift($tableNameParts);
        }
        return $tableNameParts;
    }

    public function getTemplateClassName(): string
    {
        return $this->className ?: $this->classNameFromTableName();
    }

    public function templateContents(): string
    {
        $tableInfo = new TableInfo($this->adapter);
        $stubReplacer = new ClassStubPlaceholderReplacer($this->getTemplateClassName(), $tableInfo, $this->getClassNamespace());
        return $stubReplacer->contents();
    }

    public function saveToFile(string $fileName): void
    {
        if (is_file($fileName)) {
            throw new GeneratorException("File already exists '{$fileName}'.");
        }
        $this->preparePaths(dirname($fileName));
        file_put_contents($fileName, $this->templateContents());
    }

    public function getClassNamespace(): string
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

    private function classNameFromTableName(): string
    {
        $parts = explode('_', $this->tableName);
        $parts = $this->removeTablePrefix($parts);
        $parts[] = Inflector::singularize(array_pop($parts));
        $parts = Arrays::map($parts, 'ucfirst');
        return implode('', $parts);
    }

    private function preparePaths(string $basename): void
    {
        if (!is_dir($basename)) {
            mkdir($basename, 0777, true);
        }
    }
}


