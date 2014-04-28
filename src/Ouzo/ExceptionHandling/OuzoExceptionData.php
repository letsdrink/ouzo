<?php
namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Http\ResponseMapper;
use Ouzo\Utilities\Joiner;

class OuzoExceptionData
{
    private $_httpCode;
    private $_errors;
    private $_stackTrace;

    function __construct($httpCode, $errors, $stackTrace)
    {
        $this->_errors = $errors;
        $this->_httpCode = $httpCode;
        $this->_stackTrace = $stackTrace;
    }

    public static function forException($httpCode, Exception $exception)
    {
        return new OuzoExceptionData($httpCode, array(Error::forException($exception)), $exception->getTraceAsString());
    }


    public function getErrors()
    {
        return $this->_errors;
    }

    public function getHttpCode()
    {
        return $this->_httpCode;
    }

    public function getStackTrace()
    {
        return $this->_stackTrace;
    }

    public function getHeader()
    {
        return ResponseMapper::getMessageWithHttpProtocol($this->_httpCode);
    }

    public function getMessage()
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->message;
        })->join($this->_errors);
    }
}