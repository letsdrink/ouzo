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
    public function __construct(private readonly ExceptionHandler $exceptionHandler)
    {
    }

    public function register(): void
    {
        set_exception_handler(fn(Throwable $exception) => $this->handleException($exception));
        set_error_handler(fn(...$args) => $this->handleError(...$args), E_ALL & ~E_DEPRECATED & ~E_STRICT);
        register_shutdown_function(fn() => $this->handleShutdown());
    }

    public function handleException(Throwable $exception): void
    {
        $this->exceptionHandler->handleException($exception);
    }

    public function handleError(int $errorNumber, string $errorString, string $errorFile, int $errorLine): void
    {
        if ($this->stopsExecution($errorNumber)) {
            $this->handleException(new ErrorException($errorString, $errorNumber, $errorNumber, $errorFile, $errorLine));
        } else {
            throw new ErrorException($errorString, $errorNumber, $errorNumber, $errorFile, $errorLine);
        }
    }

    public function stopsExecution($errno): bool
    {
        return match ($errno) {
            E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR => true,
            default => false
        };
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();

        if (!$this->exceptionHandler->lastErrorHandled() && $error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            $stackTrace = new StackTrace($error['file'], $error['line']);
            $exceptionData = new OuzoExceptionData(500, [new Error(0, $error['message'])], $stackTrace, [], null, $error['type']);
            $this->exceptionHandler->handleExceptionData($exceptionData);
        }
    }
}
