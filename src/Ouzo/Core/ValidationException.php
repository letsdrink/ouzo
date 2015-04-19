<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Exception;
use Ouzo\Utilities\Objects;

class ValidationException extends Exception
{
    private $_errors;

    public function __construct($message, array $errors)
    {
        parent::__construct($message . "\nErrors: " . Objects::toString($errors));
        $this->_errors = $errors;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}
