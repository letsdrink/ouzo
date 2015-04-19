<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Tools\Model\Template;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Path;

class ClassStub
{
    const FIELDS_COUNT_IN_LINE = 7;

    private $_stubContent;
    private $_attributes = array();
    private $_placeholderWithReplacements = array();
    private $shortArrays;

    public function __construct($shortArrays = false)
    {
        $this->shortArrays = $shortArrays;
        $stubFilePath = $this->_getStubFilePath();
        $this->_stubContent = file_get_contents($stubFilePath);
    }

    private function _getStubFilePath()
    {
        if ($this->shortArrays) {
            $stubFileName = 'class_short_arrays.stub';
        } else {
            $stubFileName = 'class.stub';
        }
        return Path::join(__DIR__, 'stubs', $stubFileName);
    }

    public function addColumn(DatabaseColumn $databaseColumn)
    {
        $this->_attributes[$databaseColumn->name] = $databaseColumn->type;
        return $this;
    }

    public function addPlaceholderReplacement($placeholder, $replacement)
    {
        $this->_placeholderWithReplacements[$placeholder] = $replacement;
        return $this;
    }

    public function addTableSetupItem($name, $value)
    {
        if ($value) {
            $value = sprintf("'%s' => '%s',", $name, $value);
        }
        $this->addPlaceholderReplacement("table_$name", $value);
    }

    public function addTablePrimaryKey($primaryKey)
    {
        if (empty($primaryKey)) {
            $value = sprintf("'%s' => '',", 'primaryKey');
            $this->addPlaceholderReplacement("table_primaryKey", $value);
        } else {
            $placeholderPrimaryKey = ($primaryKey != 'id') ? $primaryKey : '';
            $this->addTableSetupItem('primaryKey', $placeholderPrimaryKey);
        }
    }

    public function replacePlaceholders($replacement)
    {
        foreach ($replacement as $key => $value) {
            $searchRegExp = ($value) ? "/{($key)}/" : "/\s*{($key)}*/";
            $this->_stubContent = preg_replace($searchRegExp, $value, $this->_stubContent);
        }
        return $this;
    }

    public function getPropertiesAsString()
    {
        $properties = array();
        foreach ($this->_attributes as $name => $type) {
            $properties[] = " * @property $type $name";
        }
        return implode("\n", $properties);
    }

    public function getFieldsAsString()
    {
        $fields = array_keys($this->_attributes);
        $escapedFields = Arrays::map($fields, Functions::compose(Functions::append("'"), Functions::prepend("'")));
        for ($index = self::FIELDS_COUNT_IN_LINE; $index < sizeof($escapedFields); $index += self::FIELDS_COUNT_IN_LINE) {
            $escapedFields[$index] = "\n\t\t\t" . $escapedFields[$index];
        }
        return implode(', ', $escapedFields);
    }

    private function _getPlaceholderReplacements()
    {
        $this
            ->addPlaceholderReplacement('properties', $this->getPropertiesAsString())
            ->addPlaceholderReplacement('fields', $this->getFieldsAsString());
        return $this->_placeholderWithReplacements;
    }

    public function contents()
    {
        $this->replacePlaceholders($this->_getPlaceholderReplacements());
        return $this->_stubContent;
    }
}
