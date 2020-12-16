<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Config;
use Ouzo\I18n;
use Ouzo\UserException;
use Throwable;

class Error
{
    public $code;
    public $message;
    public $originalMessage;
    private $field;

    public function __construct($code, $message, $originalMessage = null, $field = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->originalMessage = $originalMessage ?: $message;
        $this->field = $field;
    }

    /** @param Throwable $exception */
    public static function forException($exception)
    {
        if ($exception instanceof UserException || Config::getValue('debug')) {
            return new Error($exception->getCode(), $exception->getMessage());
        }
        return new Error($exception->getCode(), I18n::t('exception.unknown'), $exception->getMessage());
    }

    public function toArray()
    {
        $array = ['message' => $this->message, 'code' => $this->code];
        if ($this->field) {
            $array['field'] = $this->field;
        }
        return $array;
    }

    public static function getByCode($code, $params = [], $prefix = 'errors.')
    {
        $message = I18n::t($prefix . $code, $params);
        return new Error($code, $message);
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getField()
    {
        return $this->field;
    }
}
