<?php

namespace Thulium;


class Validatable
{
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

    /**
     * @param Validatable[]
     */
    public function validateAssociatedCollection($validatables)
    {
        foreach ($validatables as $validatable) {
            $this->validateAssociated($validatable);
        }
    }

    public function validateNotBlank($value, $errorMessage)
    {
        if (!$value) {
            $this->_errors[] = $errorMessage;
        }
    }

    public function validateTrue($value, $errorMessage)
    {
        if (!$value) {
            $this->_errors[] = $errorMessage;
        }
    }

    public function validateUnique(array $values, $errorMessage)
    {
        if (count($values) != count(array_unique($values))) {
            $this->_errors[] = $errorMessage;
        }
    }

    public function validateDateTime($value, $errorMessage)
    {
        if (!strtotime($value)) {
            $this->_errors[] = $errorMessage;
        }
    }
}