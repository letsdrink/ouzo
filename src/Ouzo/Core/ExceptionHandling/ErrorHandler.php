<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\ExceptionHandling;

use ErrorException;
use Exception;

class ErrorHandler
{
    public function register()
    {
        set_exception_handler(array(__CLASS__, 'exceptionHandler'));
        set_error_handler(array(__CLASS__, 'errorHandler'));
        register_shutdown_function(array(__CLASS__, 'shutdownHandler'));
    }

    public static function exceptionHandler(Exception $exception)
    {
        static::getExceptionHandler()->handleException($exception);
    }

    public static function errorHandler($errorNumber, $errorString, $errorFile, $errorLine)
    {
        if (self::stopsExecution($errorNumber)) {
            self::exceptionHandler(new ErrorException($errorString, $errorNumber, 0, $errorFile, $errorLine));
        } else {
            throw new ErrorException($errorString, $errorNumber, 0, $errorFile, $errorLine);
        }
    }

    public static function stopsExecution($errno)
    {
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                return true;
        }
        return false;
    }

    /**
     * @return ExceptionHandler
     */
    protected static function getExceptionHandler()
    {
        return new ExceptionHandler();
    }

    public static function shutdownHandler()
    {
        $error = error_get_last();

        if (!ExceptionHandler::lastErrorHandled() && $error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            static::getExceptionHandler()->handleExceptionData(new OuzoExceptionData(500, array(new Error(0, $error['message'])), self::trace($error['file'], $error['line'])));
        }
    }

    private static function trace($errorFile, $errorLine)
    {
        return "$errorFile:$errorLine";
    }
}
