<?php
namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Config;
use Ouzo\I18n;

class Error
{
    public $code;
    public $message;
    public $originalMessage;

    public function __construct($code, $message, $originalMessage = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->originalMessage = $originalMessage ?: $message;
    }

    public static function forException(Exception $exception)
    {
        if (Config::getValue('debug')) {
            return new Error($exception->getCode(), $exception->getMessage());
        }
        return new Error($exception->getCode(), I18n::t('exception.unknown'), $exception->getMessage());
    }

    public function toArray()
    {
        return array('message' => $this->message, 'code' => $this->code);
    }

    public static function getByCode($code, $params = array(), $prefix = 'errors.')
    {
        $message = I18n::t($prefix . $code, $params);
        return new Error($code, $message);
    }
}