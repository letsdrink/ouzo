<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use ErrorException;

class ErrorHandler
{
    public function register(): void
    {
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        set_error_handler([__CLASS__, 'errorHandler']);
        register_shutdown_function([__CLASS__, 'shutdownHandler']);
    }

    public static function exceptionHandler($exception): void
    {
        static::getExceptionHandler()->handleException($exception);
    }

    public static function errorHandler($errorNumber, $errorString, $errorFile, $errorLine): void
    {
        if (self::stopsExecution($errorNumber)) {
            self::exceptionHandler(new ErrorException($errorString, $errorNumber, $errorNumber, $errorFile, $errorLine));
        } else {
            throw new ErrorException($errorString, $errorNumber, $errorNumber, $errorFile, $errorLine);
        }
    }

    public static function stopsExecution($errno): bool
    {
        return match ($errno) {
            E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR => true,
            default => false
        };
    }

    protected static function getExceptionHandler(): ExceptionHandler
    {
        return new ExceptionHandler();
    }

    public static function shutdownHandler(): void
    {
        $error = error_get_last();

        if (!ExceptionHandler::lastErrorHandled() && $error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            $stackTrace = new StackTrace($error['file'], $error['line']);
            $exceptionData = new OuzoExceptionData(500, [new Error(0, $error['message'])], $stackTrace, [], null, $error['type']);
            static::getExceptionHandler()->handleExceptionData($exceptionData);
        }
    }
}
