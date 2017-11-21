<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\ExceptionHandling\Error;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use Ouzo\Utilities\Strings;

class Validatable
{
    protected $_errors = [];
    protected $_errorFields = [];

    public function isValid()
    {
        $this->validate();
        $errors = $this->getErrors();
        return empty($errors);
    }

    /**
     * @return array - returns array with saved errors
     */
    public function getErrors()
    {
        return Arrays::map($this->_errors, Functions::extractField('message'));
    }

    public function getErrorObjects()
    {
        return $this->_errors;
    }

    public function getErrorFields()
    {
        return $this->_errorFields;
    }

    public function validate()
    {
        $this->_errors = [];
        $this->_errorFields = [];
    }

    public function validateAssociated(Validatable $validatable)
    {
        $validatable->validate();
        $this->_errors = array_merge($this->getErrorObjects(), $validatable->getErrorObjects());
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

    /**
     * Check whether passed string in $value parameter has 0 length or not
     * @param string $value - string to check
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateNotBlank($value, $errorMessage, $errorField = null)
    {
        if (Strings::isBlank($value)) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether value is true, if not it saves error
     * @param $value - value to check whether is true
     * (values which are considered as TRUE or FALSE are presented here http://php.net/manual/en/types.comparisons.php )
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateTrue($value, $errorMessage, $errorField = null)
    {
        if (!$value) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether array does not contain duplicate values
     * @param array $values - array to check
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateUnique(array $values, $errorMessage, $errorField = null)
    {
        if (count($values) != count(array_unique($values))) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether $value can be converted to time by "strtotime" function
     * @param string $value - string to check
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateDateTime($value, $errorMessage, $errorField = null)
    {
        if (!strtotime($value)) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether string does not exceed max length
     * @param string $value - string to check
     * @param int $maxLength - max length which doesn't cause error
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateStringMaxLength($value, $maxLength, $errorMessage, $errorField = null)
    {
        if (strlen($value) > $maxLength) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether $value is not empty
     * (table which explains that is here http://php.net/manual/en/types.comparisons.php)
     * @param $value - value to check
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateNotEmpty($value, $errorMessage, $errorField = null)
    {
        if (empty($value)) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Validate whether $value is empty
     * (table which explains that is here http://php.net/manual/en/types.comparisons.php)
     * @param $value - value to check
     * @param $errorMessage - error message
     * @param null $errorField
     */
    public function validateEmpty($value, $errorMessage, $errorField = null)
    {
        if (!empty($value)) {
            $this->error($errorMessage);
            $this->_errorFields[] = $errorField;
        }
    }

    /**
     * Method for adding error manually
     * @param string|\Ouzo\ExceptionHandling\Error $error - \Ouzo\ExceptionHandling\Error instance or new error message
     * @param int $code - error code
     */
    public function error($error, $code = 0)
    {
        $this->_errors[] = $error instanceof Error ? $error : new Error($code, $error);
    }

    /**
     * Method for batch adding errors manually
     * @param array $errors - array of \Ouzo\ExceptionHandling\Error instaces or new error messages
     * @param int $code
     */
    public function errors(array $errors, $code = 0)
    {
        foreach ($errors as $error) {
            $this->error($error, $code);
        }
    }
}
