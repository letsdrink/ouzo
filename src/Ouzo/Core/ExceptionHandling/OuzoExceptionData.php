<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use ErrorException;
use Ouzo\Http\ResponseMapper;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Strings;
use Throwable;

class OuzoExceptionData
{
    public function __construct(
        private int $httpCode,
        private array $errors,
        private StackTrace $stackTrace,
        private array $additionalHeaders = [],
        private ?string $className = null,
        private int $severity = 0
    )
    {
    }

    public static function forException(int $httpCode, Throwable $exception): OuzoExceptionData
    {
        $severity = ($exception instanceof ErrorException) ? $exception->getSeverity() : 0;
        return new OuzoExceptionData($httpCode, [Error::forException($exception)], StackTrace::forException($exception), [], get_class($exception), $severity);
    }

    /** @return Error[] */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    public function getStackTrace(): StackTrace
    {
        return $this->stackTrace;
    }

    public function getHeader(): string
    {
        return ResponseMapper::getMessageWithHttpProtocol($this->httpCode);
    }

    /** @return string[] */
    public function getAdditionalHeaders(): array
    {
        return $this->additionalHeaders;
    }

    public function getMessage(): string
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->message;
        })->join($this->errors);
    }

    public function getOriginalMessage(): string
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->originalMessage;
        })->join($this->errors);
    }

    public function getOriginalMessageWithCodes(): string
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->originalMessage . " (code: " . $value->code . ")";
        })->join($this->errors);
    }

    public function getClassName(): ?string
    {
        return $this->className;
    }

    public function getSeverity(): int
    {
        return $this->severity;
    }

    public function getSeverityAsString(): string
    {
        $coreConstants = Arrays::getValue(get_defined_constants(true), 'Core', []);
        foreach ($coreConstants as $constName => $contValue) {
            if ($contValue == $this->severity && Strings::startsWith($constName, "E_")) {
                return $constName;
            }
        }
        return "E_UNKNOWN";
    }

    function __toString(): string
    {
        return __CLASS__ . Objects::toString(get_object_vars($this));
    }
}
