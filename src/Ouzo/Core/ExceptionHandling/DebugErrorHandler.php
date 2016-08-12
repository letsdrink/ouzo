<?php

namespace Ouzo\ExceptionHandling;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugErrorHandler extends ErrorHandler
{
    public function register()
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
        set_error_handler(array(__CLASS__, 'errorHandler'));
        register_shutdown_function(array(__CLASS__, 'shutdownHandler'));
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

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        self::getRun()->handleError($errno, $errstr, $errfile, $errline);
    }

    public static function shutdownHandler()
    {
        self::getRun()->handleShutdown();
    }
}