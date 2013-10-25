<?php
namespace Ouzo\Helper;

use Exception;
use Ouzo\I18n;
use Ouzo\Utilities\Arrays;

class AutoLabelModelFromBuilder
{
    private $_object;
    private $_objectName;

    public function __construct($object, $objectName)
    {
        $this->_object = $object;
        $this->_objectName = $objectName;
    }

    private function translate($id, array $options = array())
    {
        return I18n::t(Arrays::getValue($options, 'translation') ? : ($this->_objectName . '.' . $id));
    }

    public function textField($id, array $options = array())
    {
        $this->_updateOptionsIfError($id, $options);
        return textField($this->translate($id, $options), $this->getName($id), $this->_object->$id, $options);
    }

    public function dateField($id, array $options = array())
    {
        try {
            $dateValue = formatDate($this->_object->$id);
        } catch (Exception $e) {
            $dateValue = $this->_object->$id;
        }
        $this->_updateOptionsIfError($id, $options);
        return textField($this->translate($id), $this->getName($id), $dateValue, $options);
    }

    public function textArea($id, array $size, array $options = array())
    {
        $this->_updateOptionsIfError($id, $options);
        return textArea($this->translate($id), $this->getName($id), $this->_object->$id, $size, $options);
    }

    public function selectField($id, array $options, $defaultOption = null, $size = 1)
    {
        $this->_updateOptionsIfError($id, $options);
        return selectField($this->translate($id), $this->getName($id), $this->_object->$id, $options, $defaultOption, $size);
    }

    public function hiddenField($id, $value = null)
    {
        return hiddenField(array('name' => $this->getName($id), 'value' => $value ? $value : $this->_object->$id, 'id' => $id));
    }

    public function start($url, $method = 'post', $id = null)
    {
        $attr = array('class' => 'form');
        if ($id) {
            $attr['id'] = $id;
        }
        return formTag($url, $method, $attr);
    }

    public function end()
    {
        return endTag();
    }

    private function getName($id)
    {
        return $this->_objectName . '[' . $id . ']';
    }

    private function _updateOptionsIfError($id, array &$options)
    {
        $errorFields = $this->_object->getErrorFields();
        if (in_array($id, $errorFields)) {
            $options['error'] = true;
        }
    }

    public function getObject()
    {
        return $this->_object;
    }
} 