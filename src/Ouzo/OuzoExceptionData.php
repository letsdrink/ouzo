<?php
namespace Ouzo;

use Exception;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;

class OuzoExceptionData
{
    protected static $_messages = array(
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        418 => '418 I\'m a teapot',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    );

    private $_httpCode;
    private $_errors;
    private $_stackTrace;

    function __construct($_httpCode, $_errors, $_stackTrace)
    {
        $this->_errors = $_errors;
        $this->_httpCode = $_httpCode;
        $this->_stackTrace = $_stackTrace;
    }

    public static function forException($_httpCode, Exception $exception)
    {
        return new OuzoExceptionData($_httpCode, array(Error::forException($exception)), $exception->getTraceAsString());
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
        //TODO if not exists return HTTP/1.1 500 Internal Server Error
        return 'HTTP/1.1' . Arrays::getValue(self::$_messages, $this->_httpCode);
    }

    public function getMessage()
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->message;
        })->join($this->_errors);
    }
}