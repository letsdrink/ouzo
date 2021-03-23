<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use JetBrains\PhpStorm\Pure;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugErrorHandler extends ErrorHandler
{
    public function register(): void
    {
        set_exception_handler(fn(Throwable $exception) => DebugErrorHandler::exceptionHandler($exception));
        set_error_handler(fn(...$args) => DebugErrorHandler::errorHandler(...$args));
        register_shutdown_function(fn() => DebugErrorHandler::shutdownHandler());
    }

    protected static function getRun(): Run
    {
        $run = new Run();
        $run->pushHandler(new PrettyPageHandler());
        $run->pushHandler(new DebugErrorLogHandler());
        return $run;
    }

    #[Pure]
    protected static function getExceptionHandler(): ExceptionHandler
    {
        return new DebugExceptionHandler();
    }

    public static function errorHandler(int $errorNumber, string $errorString, string $errorFile, int $errorLine): void
    {
        self::getRun()->handleError($errorNumber, $errorString, $errorFile, $errorLine);
    }

    public static function shutdownHandler(): void
    {
        self::getRun()->handleShutdown();
    }
}
