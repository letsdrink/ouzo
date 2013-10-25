<?php
namespace Ouzo;

use Ouzo\Utilities\Strings;

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
        $this->_errorFields = array();
    }

    public function validateAssociated(Validatable $validatable)
    {
        $validatable->validate();
        $this->_errors = array_merge($this->_errors, $validatable->getErrors());
        $this->_errorFields = array_merge($this->_errorFields, $validatable->getErrorFields());
    }

    /**
     * @param Validatable []
     */
    public function validateAssociatedCollection($validatables)
    {
        foreach ($validatables as $validatable) {
            $this->validateAssociated($validatable);
        }
    }

    public function validateNotBlank($value, $errorMessage, $errorField = null)
    {
        if (Strings::isBlank($value)) {
            $this->_errors[] = $errorMessage;
            $this->_errorFields[] = $errorField;
        }
    }

    public function validateTrue($value, $errorMessage, $errorField = null)
    {
        if (!$value) {
            $this->_errors[] = $errorMessage;
            $this->_errorFields[] = $errorField;
        }
    }

    public function validateUnique(array $values, $errorMessage, $errorField = null)
    {
        if (count($values) != count(array_unique($values))) {
            $this->_errors[] = $errorMessage;
            $this->_errorFields[] = $errorField;
        }
    }

    public function validateDateTime($value, $errorMessage, $errorField = null)
    {
        if (!strtotime($value)) {
            $this->_errors[] = $errorMessage;
            $this->_errorFields[] = $errorField;
        }
    }

    public function validateStringMaxLength($value, $maxLength, $errorMessage, $errorField = null)
    {
        if ((strlen($value) - 1) > $maxLength) {
            $this->_errors[] = $errorMessage;
            $this->_errorFields[] = $errorField;
        }
    }
}