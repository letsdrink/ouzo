<?php

namespace Ouzo;

use Exception;
use Ouzo\Utilities\Objects;

class ValidationException extends Exception
{
    private $_errors;

    function __construct($message, array $errors)
    {
        parent::__construct($message . "\nErrors: " . Objects::toString($errors));
        $this->_errors = $errors;
    }

    public function getErrors()
    {
        return $this->_errors;
    }
}