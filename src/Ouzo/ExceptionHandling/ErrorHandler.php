<?php
namespace Ouzo\ExceptionHandling;

use ErrorException;
use Exception;
use Ouzo\ContentType;
use Ouzo\Logger\Logger;
use Ouzo\Routing\RouterException;
use Ouzo\UserException;
use Ouzo\Utilities\Objects;
use Ouzo\ViewPathResolver;

class ErrorHandler
{
    static $errorHandled = false;

    public static function exceptionHandler(Exception $exception)
    {
        if ($exception instanceof UserException) {
            self::_renderUserError(OuzoExceptionData::forException(500, $exception));
        } elseif ($exception instanceof RouterException) {
            self::_renderNotFoundError(OuzoExceptionData::forException(404, $exception));
        } elseif ($exception instanceof OuzoException) {
            self::_handleError($exception->asExceptionData());
        } else {
            self::_handleError(OuzoExceptionData::forException(500, $exception));
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

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (self::stopsExecution($errno)) {
            self::_handleError(new ErrorException($errstr, $errno, 0, $errfile, $errline));
        } else {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    private static function _clearOutputBuffers()
    {
        while (ob_get_level()) {
            if (!ob_end_clean()) {
                break;
            }
        }
    }

    private static function _handleError($exception)
    {
        self::_renderError($exception);
    }

    private static function _renderUserError($exception)
    {
        header("Contains-Error-Message: User");
        self::_renderError($exception, 'user_exception');
    }

    private static function _renderNotFoundError($exception)
    {
        self::_renderError($exception);
    }

    private static function _renderError(OuzoExceptionData $exceptionData, $viewName = 'exception')
    {
        try {
            $errorMessage = $exceptionData->getMessage();
            $errorTrace = $exceptionData->getStackTrace();
            self::$errorHandled = true;
            Logger::getLogger(__CLASS__)->error($errorMessage);
            Logger::getLogger(__CLASS__)->error(Objects::toString($errorTrace));
            self::_clearOutputBuffers();
            header($exceptionData->getHeader());
            header('Content-type: ' . ContentType::value());

            /** @noinspection PhpIncludeInspection */
            require(ViewPathResolver::resolveViewPath($viewName));
        } catch (Exception $e) {
            echo "Framework critical error. Exception thrown in exception handler.<br>\n";
            echo "<hr>\n";
            echo "Message: " . $e->getMessage() . "<br>\n";
            echo "Trace: " . $e->getTraceAsString() . "<br>\n";
        }
    }

    static public function shutdownHandler()
    {
        $error = error_get_last();

        if (!self::$errorHandled && $error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            self::_handleError(new OuzoExceptionData(500, array(new Error(0, $error['message'])), self::trace($error['file'], $error['line'])));
        }
    }

    private static function trace($errfile, $errline)
    {
        return "$errfile:$errline";
    }
}