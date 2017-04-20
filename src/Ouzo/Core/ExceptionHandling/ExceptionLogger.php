<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use ErrorException;
use Ouzo\Logger\Logger;

class ExceptionLogger
{
    /** @var OuzoExceptionData */
    private $exceptionData;

    public function __construct(OuzoExceptionData $exceptionData)
    {
        $this->exceptionData = $exceptionData;
    }

    public static function newInstance(OuzoExceptionData $exceptionData)
    {
        return new self($exceptionData);
    }

    public function log()
    {
        $className = $this->exceptionData->getClassName();
        $originalMessage = $this->exceptionData->getOriginalMessage();
        $httpCode = $this->exceptionData->getHttpCode();
        $trace = $this->exceptionData->getStackTrace();
        $traceString = $trace->getTraceAsString();
        $source = $trace->getFile() . ":" . $trace->getLine();
        $isErrorException = is_subclass_of($className, ErrorException::class);

        $message = "[HTTP $httpCode] ";

        if ($className && !$isErrorException) {
            $message .= "Exception '$className' with message '$originalMessage'";
        } else {
            $message .= "Error '$originalMessage'";
        }

        if ($this->exceptionData->getSeverity()) {
            $message .= ", severity {$this->exceptionData->getSeverityAsString()}";
        }

        $message .= " in $source.";

        if ($traceString) {
            $message .= "\nStack trace:\n$traceString";
        }

        Logger::getLogger(__CLASS__)->error($message);
    }
}