<?php
namespace Ouzo\Api;

use Exception;

class ValidationException extends Exception
{
    const HTTP_CODE = 400;

    function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}