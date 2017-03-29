<?php

namespace Ouzo\ExceptionHandling;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugErrorHandler extends ErrorHandler
{
    public function register()
    {
        set_exception_handler([__CLASS__, 'exceptionHandler']);
        set_error_handler([__CLASS__, 'errorHandler']);
        register_shutdown_function([__CLASS__, 'shutdownHandler']);
    }

    protected static function getRun()
    {
        $run = new Run();
        $run->pushHandler(new PrettyPageHandler());
        return $run;
    }

    /**
     * @return ExceptionHandler
     */
    protected static function getExceptionHandler()
    {
        return new DebugExceptionHandler();
    }

    public static function errorHandler($errorNumber, $errorString, $errorFile, $errorLine)
    {
        self::getRun()->handleError($errorNumber, $errorString, $errorFile, $errorLine);
    }

    public static function shutdownHandler()
    {
        self::getRun()->handleShutdown();
    }
}