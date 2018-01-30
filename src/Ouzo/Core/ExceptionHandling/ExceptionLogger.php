<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use ErrorException;
use Exception;
use Ouzo\Logger\Logger;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;

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

    public static function forException(Exception $exception, $httpCode = 500)
    {
        return new self(OuzoExceptionData::forException($httpCode, $exception));
    }

    public function log()
    {
        $message = $this->getMessage();
        Logger::getLogger(__CLASS__)->error($message);
    }

    public function getMessage()
    {
        $className = $this->exceptionData->getClassName();
        $originalMessage = $this->exceptionData->getOriginalMessageWithCodes();
        $httpCode = $this->exceptionData->getHttpCode();
        $trace = $this->exceptionData->getStackTrace();
        $traceString = $trace->getTraceAsString();
        $source = $trace->getFile() . ":" . $trace->getLine();
        $isErrorException = is_subclass_of($className, ErrorException::class);

        $message = "Exception: $originalMessage";
        $message .= "\n------------------------------------------------------------------------------------------------------------------------------------";
        $message .= "\nHTTP status: $httpCode";

        if ($className && !$isErrorException) {
            $message .= "\nException: $className\nMessage: $originalMessage";
        } else {
            $message .= "\nError: $originalMessage";
        }

        if ($this->exceptionData->getSeverity()) {
            $message .= "\nSeverity: {$this->exceptionData->getSeverityAsString()}";
        }

        $message .= "\nLine: $source";

        if ($traceString) {
            $message .= "\nStack trace:\n$traceString";
        }
        $message .= "\nREQUEST_METHOD = " . Arrays::getValue($_SERVER, 'REQUEST_METHOD');
        $message .= "\nSCRIPT_URI = " . Arrays::getValue($_SERVER, 'SCRIPT_URI');
        $message .= "\nREQUEST_URI = " . Arrays::getValue($_SERVER, 'REQUEST_URI');
        $message .= "\nREDIRECT_URL = " . Arrays::getValue($_SERVER, 'REDIRECT_URL');
        if (!empty($_GET)) {
            $message .= "\nGET = " . Objects::toString($_GET);
        }
        if (!empty($_POST)) {
            $message .= "\nPOST = " . Objects::toString($_POST);
        }
        $message .= "\n------------------------------------------------------------------------------------------------------------------------------------";
        return $message;
    }
}