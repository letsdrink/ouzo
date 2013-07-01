<?php

namespace Thulium;


class Validatable {
    protected $_errors = array();
    protected $_errorFields = array();

    public function isValid()
    {
        $this->validate();
        $errors = $this->getErrors();
        return empty($errors);
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getErrorFields()
    {
        return $this->_errorFields;
    }

    public function validate()
    {
        $this->_errors = array();
    }

    public function validateAssociated(Validatable $validatable)
    {
        $validatable->validate();
        $this->_errors = array_merge($this->_errors, $validatable->getErrors());
        $this->_errorFields = array_merge($this->_errorFields, $validatable->getErrorFields());
    }
}