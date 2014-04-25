<?php


namespace Ouzo\Tools\Model\Template;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Path;

class ClassStub
{
    private $_stubContent;
    private $_attributes = array();
    private $_placeholderWithReplacements = array();

    const FIELDS_COUNT_IN_LINE = 7;

    function __construct()
    {
        $stubFilePath = $this->_getStubFilePath();
        $this->_stubContent = file_get_contents($stubFilePath);
    }

    private function _getStubFilePath()
    {
        $stubFileName = 'class.stub';
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
        for($index = self::FIELDS_COUNT_IN_LINE; $index < sizeof($escapedFields); $index += self::FIELDS_COUNT_IN_LINE){
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