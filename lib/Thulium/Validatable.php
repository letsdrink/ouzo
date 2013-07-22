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

    protected function validateAssociated(Validatable $validatable)
    {
        $validatable->validate();
        $this->_errors = array_merge($this->_errors, $validatable->getErrors());
        $this->_errorFields = array_merge($this->_errorFields, $validatable->getErrorFields());
    }

    /**
     * @param Validatable[]
     */
    protected function validateAssociatedCollection($validatables)
    {
        foreach ($validatables as $validatable) {
            $this->validateAssociated($validatable);
        }
    }

    protected function validateNotBlank($value, $errorMessage)
    {
        if (!$value) {
            $this->_errors[] = $errorMessage;
        }
    }

    protected function validateTrue($value, $errorMessage)
    {
        if (!$value) {
            $this->_errors[] = $errorMessage;
        }
    }

    protected function validateUnique(array $values, $errorMessage)
    {
        if (count($values) != count(array_unique($values))) {
            $this->_errors[] = $errorMessage;
        }
    }

}