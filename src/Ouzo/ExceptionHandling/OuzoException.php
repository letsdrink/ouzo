<?php
namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Utilities\Arrays;

class OuzoException extends Exception
{
    private $_httpCode;
    private $_errors;

    public function __construct($httpCode, $errors)
    {
        $this->_httpCode = $httpCode;
        $this->_errors = Arrays::toArray($errors);
        parent::__construct();
    }

    public function getHttpCode()
    {
        return $this->_httpCode;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function asExceptionData()
    {
        return new OuzoExceptionData($this->_httpCode, $this->_errors, $this->getTraceAsString());
    }
}