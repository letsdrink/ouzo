<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use ErrorException;
use Exception;
use Ouzo\Http\ResponseMapper;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Strings;

class OuzoExceptionData
{
    /** @var int */
    private $httpCode;
    /** @var Error[] */
    private $errors;
    /** @var StackTrace */
    private $stackTrace;
    /** @var string[] */
    private $additionalHeaders;
    /** @var string */
    private $className;
    /**@var int */
    private $severity;

    public function __construct($httpCode, $errors, $stackTrace, $additionalHeaders = [], $className = null, $severity = 0)
    {
        $this->errors = $errors;
        $this->httpCode = $httpCode;
        $this->stackTrace = $stackTrace;
        $this->additionalHeaders = $additionalHeaders;
        $this->className = $className;
        $this->severity = $severity;
    }

    public static function forException($httpCode, Exception $exception)
    {
        $severity = ($exception instanceof ErrorException) ? $exception->getSeverity() : 0;
        return new OuzoExceptionData($httpCode, [Error::forException($exception)], StackTrace::forException($exception), [], get_class($exception), $severity);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getHttpCode()
    {
        return $this->httpCode;
    }

    public function getStackTrace()
    {
        return $this->stackTrace;
    }

    public function getHeader()
    {
        return ResponseMapper::getMessageWithHttpProtocol($this->httpCode);
    }

    public function getAdditionalHeaders()
    {
        return $this->additionalHeaders;
    }

    public function getMessage()
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->message;
        })->join($this->errors);
    }

    public function getOriginalMessage()
    {
        return Joiner::on(', ')->map(function ($key, $value) {
            return $value->originalMessage;
        })->join($this->errors);
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getSeverity()
    {
        return $this->severity;
    }

    public function getSeverityAsString()
    {
        $coreConstants = Arrays::getValue(get_defined_constants(true), 'Core', []);
        foreach ($coreConstants as $constName => $contValue) {
            if ($contValue == $this->severity && Strings::startsWith($constName, "E_")) {
                return $constName;
            }
        }
        return "E_UNKNOWN";
    }
}
