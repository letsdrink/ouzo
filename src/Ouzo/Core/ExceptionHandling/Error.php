<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use JetBrains\PhpStorm\ArrayShape;
use Ouzo\Config;
use Ouzo\I18n;
use Ouzo\UserException;
use Throwable;

class Error
{
    public ?int $code;
    public ?string $message;
    public ?string $originalMessage;
    private ?string $field;

    public function __construct(
        ?int $code,
        ?string $message,
        ?string $originalMessage = null,
        ?string $field = null
    )
    {
        $this->code = $code;
        $this->message = $message;
        $this->originalMessage = $originalMessage ?: $message;
        $this->field = $field;
    }

    public static function forException(Throwable $exception): Error
    {
        if ($exception instanceof UserException || Config::getValue('debug')) {
            return new Error($exception->getCode(), $exception->getMessage());
        }
        return new Error($exception->getCode(), I18n::t('exception.unknown'), $exception->getMessage());
    }

    #[ArrayShape(['message' => "null|string", 'code' => "null|int", 'field' => "null|string"])]
    public function toArray(): array
    {
        $array = ['message' => $this->message, 'code' => $this->code];
        if ($this->field) {
            $array['field'] = $this->field;
        }
        return $array;
    }

    public static function getByCode($code, $params = [], $prefix = 'errors.'): Error
    {
        $message = I18n::t($prefix . $code, $params);
        return new Error($code, $message);
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}
