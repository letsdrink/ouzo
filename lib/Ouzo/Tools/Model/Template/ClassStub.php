<?php


namespace Ouzo\Tools\Model\Template;


use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class ClassStub
{
    private $_stubFile;
    private $_stubContent;
    private $_fields = array();
    private $_placeholderWithReplacements = array();

    const FIELDS_COUNT_IN_LINE = 7;

    function __construct($stubFile = 'class.stub')
    {
        $this->_stubFile = $stubFile;
        $this->_stubContent = file_get_contents(Path::join(__DIR__, 'stubs', $stubFile));
    }

    public function addFiled($name, $type)
    {
        $this->_fields[$name] = $type;
        return $this;
    }

    public function addPlaceholderReplacement($placeholder, $replacement)
    {
        $this->_placeholderWithReplacements[$placeholder] = $replacement;
        return $this;
    }

    public function replacePlaceholders($replacement)
    {
        foreach ($replacement as $key => $value) {
            $this->_stubContent = preg_replace("/{($key)}/", $value, $this->_stubContent);
        }
        return $this;
    }

    private function _getPropertiesAsString()
    {
        $propertiesString = '';
        foreach ($this->_fields as $name => $type) {
            $propertiesString .= " * @property $type $name\n";
        }
        return $propertiesString;
    }

    private function _getFieldsAsString()
    {
        $fields = array_keys($this->_fields);
        $escapedFields = Arrays::map($fields, function ($field) {
            return "'$field'";
        });
        $escapedFields = $this->_fieldsInNewLines($escapedFields);
        return implode(', ', $escapedFields);
    }

    private function _getPlaceholderReplacements()
    {
        $this
            ->addPlaceholderReplacement('properties', $this->_getPropertiesAsString())
            ->addPlaceholderReplacement('fields', $this->_getFieldsAsString());
        return $this->_placeholderWithReplacements;
    }

    public function contents()
    {
        $this->replacePlaceholders($this->_getPlaceholderReplacements());
        return $this->_stubContent;
    }

    private function _fieldsInNewLines($escapedFields)
    {
        for ($index = 1; $index < sizeof($escapedFields); $index += self::FIELDS_COUNT_IN_LINE) {
            if (array_key_exists($index, $escapedFields)) {
                $escapedFields[$index] = "\n\t\t\t" . $escapedFields[$index];
            }
        }
        return $escapedFields;
    }
}