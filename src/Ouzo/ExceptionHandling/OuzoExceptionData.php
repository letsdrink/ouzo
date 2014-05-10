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
    private $_additionalHeaders;

    public function __construct($httpCode, $errors, $stackTrace, $additionalHeaders = array())
    {
        $this->_errors = $errors;
        $this->_httpCode = $httpCode;
        $this->_stackTrace = $stackTrace;
        $this->_additionalHeaders = $additionalHeaders;
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

    public function getAdditionalHeaders()
    {
        return $this->_additionalHeaders;
    }

    public function getMessage()
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->message;
        })->join($this->_errors);
    }
}