<?php
namespace Ouzo\ExceptionHandling;

use Exception;

class Error
{
    public $code;
    public $message;

    function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public static function forException(Exception $exception)
    {
        return new Error($exception->getCode(), $exception->getMessage());
    }

    public function toArray()
    {
        return array('message' => $this->message, 'code' => $this->code);
    }
}