<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugErrorHandler extends ErrorHandler
{
    public function register(): void
    {
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        set_error_handler([__CLASS__, 'errorHandler']);
        register_shutdown_function([__CLASS__, 'shutdownHandler']);
    }

    protected static function getRun(): Run
    {
        $run = new Run();
        $run->pushHandler(new PrettyPageHandler());
        $run->pushHandler(new DebugErrorLogHandler());
        return $run;
    }

    protected static function getExceptionHandler(): ExceptionHandler
    {
        return new DebugExceptionHandler();
    }

    public static function errorHandler($errorNumber, $errorString, $errorFile, $errorLine): void
    {
        self::getRun()->handleError($errorNumber, $errorString, $errorFile, $errorLine);
    }

    public static function shutdownHandler(): void
    {
        self::getRun()->handleShutdown();
    }
}
