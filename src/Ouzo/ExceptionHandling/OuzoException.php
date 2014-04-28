<?php
namespace Ouzo\ExceptionHandling;

use Exception;

class OuzoException extends Exception
{
    private $_httCode;
    private $_errors;

    public function __construct($httCode, $errors)
    {
        $this->_httCode = $httCode;
        $this->_errors = $errors;
        parent::__construct();
    }

    public function getHttCode()
    {
        return $this->_httCode;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function asExceptionData()
    {
        return new OuzoExceptionData($this->_httCode, $this->_errors, $this->getTraceAsString());
    }
}