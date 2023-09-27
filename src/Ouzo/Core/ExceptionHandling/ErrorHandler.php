<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use ErrorException;
use Throwable;

class ErrorHandler
{
    public function register(): void
    {
        set_exception_handler(fn(Throwable $exception) => static::exceptionHandler($exception));
        set_error_handler(fn(...$args) => static::errorHandler(...$args), E_ALL & ~E_DEPRECATED & ~E_STRICT);
        register_shutdown_function(fn() => static::shutdownHandler());
    }

    public static function exceptionHandler(Throwable $exception): void
    {
        static::getExceptionHandler()->handleException($exception);
    }

    public static function errorHandler(int $errorNumber, string $errorString, string $errorFile, int $errorLine): void
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
