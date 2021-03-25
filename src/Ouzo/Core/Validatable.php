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
    protected array $errors = [];
    protected array $errorFields = [];

    public function isValid(): bool
    {
        $this->validate();
        $errors = $this->getErrors();
        return empty($errors);
    }

    /**
     * @return array - returns array with saved errors
     */
    public function getErrors(): array
    {
        return Arrays::map($this->errors, Functions::extractField('message'));
    }

    public function getErrorObjects(): array
    {
        return $this->errors;
    }

    public function getErrorFields(): array
    {
        return $this->errorFields;
    }

    public function validate(): void
    {
        $this->errors = [];
        $this->errorFields = [];
    }

    public function validateAssociated(Validatable $validatable): void
    {
        $validatable->validate();
        $this->errors = array_merge($this->getErrorObjects(), $validatable->getErrorObjects());
        $this->errorFields = array_merge($this->errorFields, $validatable->getErrorFields());
    }

    /** @param Validatable[] */
    public function validateAssociatedCollection(array $validatables): void
    {
        foreach ($validatables as $validatable) {
            $this->validateAssociated($validatable);
        }
    }

    /** Check whether passed string in `$value` parameter has 0 length or not */
    public function validateNotBlank(string $value, string|Error $errorMessage, string $errorField = null): void
    {
        if (Strings::isBlank($value)) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether value is true, if not it saves error
     * (values which are considered as TRUE or FALSE are presented here http://php.net/manual/en/types.comparisons.php )
     */
    public function validateTrue(mixed $value, string|Error $errorMessage, string $errorField = null): void
    {
        if (!$value) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /** Checks whether array does not contain duplicate values */
    public function validateUnique(array $values, string|Error $errorMessage, string $errorField = null): void
    {
        if (count($values) != count(array_unique($values))) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /** Checks whether $value can be converted to time by "strtotime" function */
    public function validateDateTime(string $value, string|Error $errorMessage, string $errorField = null): void
    {
        if (!strtotime($value)) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /** Checks whether string does not exceed max length */
    public function validateStringMaxLength(string $value, int $maxLength, string|Error $errorMessage, string $errorField = null): void
    {
        if (strlen($value) > $maxLength) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /**
     * Checks whether $value is not empty
     * (table which explains that is here http://php.net/manual/en/types.comparisons.php)
     */
    public function validateNotEmpty(mixed $value, string|Error $errorMessage, string $errorField = null): void
    {
        if (empty($value)) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /**
     * Validate whether $value is empty
     * (table which explains that is here http://php.net/manual/en/types.comparisons.php)
     */
    public function validateEmpty(mixed $value, string|Error $errorMessage, string $errorField = null): void
    {
        if (!empty($value)) {
            $this->error($errorMessage);
            $this->errorFields[] = $errorField;
        }
    }

    /** Method for adding error manually */
    public function error(string|Error $error, int $code = 0): void
    {
        $this->errors[] = $error instanceof Error ? $error : new Error($code, $error);
    }

    /**
     * Method for batch adding errors manually
     * @param string[]|Error[] $errors
     */
    public function errors(array $errors, int $code = 0): void
    {
        foreach ($errors as $error) {
            $this->error($error, $code);
        }
    }
}
