<?php
namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Config;
use Ouzo\I18n;

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
        if (Config::getValue('debug')) {
            return new Error($exception->getCode(), $exception->getMessage());
        }
        return new Error($exception->getCode(), I18n::t('exception.unknown'));
    }

    public function toArray()
    {
        return array('message' => $this->message, 'code' => $this->code);
    }
}