<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\ExceptionHandling;

use ErrorException;
use Exception;
use Ouzo\Logger\Logger;
use Ouzo\Response\ResponseTypeResolve;
use Ouzo\Routing\RouterException;
use Ouzo\UserException;
use Ouzo\Utilities\Objects;
use Ouzo\ViewPathResolver;

class ErrorHandler
{
    public static $errorHandled = false;

    public static function exceptionHandler(Exception $exception)
    {
        ErrorHandler::instance()->handleException($exception);
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (self::stopsExecution($errno)) {
            self::exceptionHandler(new ErrorException($errstr, $errno, 0, $errfile, $errline));
        } else {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    public function handleException($exception)
    {
        if ($exception instanceof UserException) {
            $this->renderUserError(OuzoExceptionData::forException(500, $exception));
        } elseif ($exception instanceof RouterException) {
            $this->renderNotFoundError(OuzoExceptionData::forException(404, $exception));
        } elseif ($exception instanceof OuzoException) {
            $this->handleError($exception->asExceptionData());
        } else {
            $this->handleError(OuzoExceptionData::forException(500, $exception));
        }
    }

    public function handleExceptionData(OuzoExceptionData $exceptionData)
    {
        $this->handleError($exceptionData);
    }

    private static function instance()
    {
        return new self();
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

    private function clearOutputBuffers()
    {
        while (ob_get_level()) {
            if (!ob_end_clean()) {
                break;
            }
        }
    }

    private function handleError($exception)
    {
        $this->renderError($exception);
    }

    private function renderUserError($exception)
    {
        header("Contains-Error-Message: User");
        $this->renderError($exception, 'user_exception');
    }

    private function renderNotFoundError($exception)
    {
        $this->renderError($exception);
    }

    private function renderError(OuzoExceptionData $exceptionData, $viewName = 'exception')
    {
        try {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $errorMessage = $exceptionData->getMessage();
            $errorTrace = $exceptionData->getStackTrace();
            self::$errorHandled = true;
            Logger::getLogger(__CLASS__)->error($exceptionData->getOriginalMessage());
            Logger::getLogger(__CLASS__)->error(Objects::toString($errorTrace));
            $this->clearOutputBuffers();
            header($exceptionData->getHeader());
            $responseType = ResponseTypeResolve::resolve();
            header('Content-type: ' . $responseType);

            $additionalHeaders = $exceptionData->getAdditionalHeaders();
            array_walk($additionalHeaders, function ($header) {
                header($header);
            });

            /** @noinspection PhpIncludeInspection */
            require(ViewPathResolver::resolveViewPath($viewName, $responseType));
        } catch (Exception $e) {
            echo "Framework critical error. Exception thrown in exception handler.<br>\n";
            echo "<hr>\n";
            echo "Message: " . $e->getMessage() . "<br>\n";
            echo "Trace: " . $e->getTraceAsString() . "<br>\n";
        }
    }

    public static function shutdownHandler()
    {
        $error = error_get_last();

        if (!self::$errorHandled && $error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            ErrorHandler::instance()->handleExceptionData(new OuzoExceptionData(500, array(new Error(0, $error['message'])), self::trace($error['file'], $error['line'])));
        }
    }

    private static function trace($errfile, $errline)
    {
        return "$errfile:$errline";
    }
}
