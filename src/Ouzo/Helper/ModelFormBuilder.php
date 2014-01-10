<?php
namespace Ouzo\Helper;

use Ouzo\I18n;
use Ouzo\Utilities\Joiner;

class ModelFormBuilder
{
    private $_object;

    public function __construct($object)
    {
        $this->_object = $object;
    }

    private function _objectName()
    {
        return strtolower($this->_object->getModelName());
    }

    private function _generateId($name)
    {
        return $this->_objectName() . '_' . $name;
    }

    private function _generateName($name)
    {
        return $this->_objectName() . '[' . $name . ']';
    }

    private function _generatePredefinedAttributes($field)
    {
        $id = $this->_generateId($field);
        $name = $this->_generateName($field);
        $attributes = array('id' => $id, 'name' => $name);

        if (in_array($field, $this->_object->getErrorFields())) {
            $attributes['class'] = 'error';
        }
        return $attributes;
    }

    private function _translate($field)
    {
        return I18n::t($this->_objectName() . '.' . $field);
    }

    public function label($field, array $options = array())
    {
        return labelTag($this->_generateId($field), $this->_translate($field), $options);
    }

    public function textField($field, array $options = array())
    {
        $attributes = $this->_generatePredefinedAttributes($field);
        $attributes = $this->_mergeAttributes($attributes, $options);
        return textFieldTag($this->_object->$field, $attributes);
    }

    public function textArea($field, array $options = array())
    {
        $attributes = $this->_generatePredefinedAttributes($field);
        $attributes = $this->_mergeAttributes($attributes, $options);
        return textAreaTag($this->_object->$field, $attributes);
    }

    public function selectField($field, array $items, $options = array(), $defaultOption = null)
    {
        $attributes = $this->_generatePredefinedAttributes($field);
        $attributes = $this->_mergeAttributes($attributes, $options);
        return selectTag($items, array($this->_object->$field), $attributes, $defaultOption);
    }

    public function hiddenField($field, $value = null, $options = array())
    {
        $attributes = $this->_generatePredefinedAttributes($field);
        $attributes = $this->_mergeAttributes($attributes, $options);
        return hiddenTag($value ? $value : $this->_object->$field, $attributes);
    }

    public function passwordField($field, array $options = array())
    {
        $attributes = $this->_generatePredefinedAttributes($field);
        $attributes = $this->_mergeAttributes($attributes, $options);
        return passwordFieldTag($this->_object->$field, $attributes);
    }

    public function checkboxField($field, array $options = array())
    {
        $attributes = $this->_generatePredefinedAttributes($field);
        $attributes = $this->_mergeAttributes($attributes, $options);
        $value = $this->_object->$field;
        $checked = !empty($value);
        return checkboxTag('1', $checked, $attributes);
    }

    public function start($url, $method = 'post', $attributes = array())
    {
        return formTag($url, $method, $attributes);
    }

    public function end()
    {
        return endTag();
    }

    public function getObject()
    {
        return $this->_object;
    }

    private function _mergeAttributes($attributes, $options)
    {
        if (isset($options['class'])) {
            $options['class'] = Joiner::on(' ')->skipNulls()->join(array($options['class'], $attributes['class']));
        }
        return array_merge($attributes, $options);
    }
}