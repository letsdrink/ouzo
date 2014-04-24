<?php
namespace Ouzo;

use ErrorException;
use Exception;
use Ouzo\Logger\Logger;
use Ouzo\Routing\RouterException;
use Ouzo\Utilities\Objects;

class Error
{
    static $errorHandled = false;

    public static function exceptionHandler(Exception $exception)
    {
        if ($exception instanceof UserException) {
            self::_renderUserError($exception->getMessage(), $exception->getTraceAsString());
        } elseif ($exception instanceof RouterException) {
            self::_renderNotFoundError($exception->getMessage(), $exception->getTraceAsString());
        } else {
            self::_handleError($exception->getMessage(), $exception->getTraceAsString());
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
            self::_handleError($errstr, self::trace($errfile, $errline));
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

    private static function _handleError($errorMessage, $errorTrace)
    {
        self::_renderError($errorMessage, $errorTrace, "HTTP/1.1 500 Internal Server Error", 'exception');
    }

    private static function _renderUserError($errorMessage, $errorTrace)
    {
        header("Contains-Error-Message: User");
        self::_renderError($errorMessage, $errorTrace, "HTTP/1.1 500 Internal Server Error", 'user_exception');
    }

    private static function _renderNotFoundError($errorMessage, $errorTrace)
    {
        self::_renderError($errorMessage, $errorTrace, "HTTP/1.1 404 Not Found", '404');
    }

    private static function _renderError($errorMessage, $errorTrace, $header, $viewName)
    {
        try {
            self::$errorHandled = true;
            Logger::getLogger(__CLASS__)->error($errorMessage);
            Logger::getLogger(__CLASS__)->error(Objects::toString($errorTrace));
            /** @noinspection PhpIncludeInspection */
            self::_clearOutputBuffers();
            header($header);
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
            self::_handleError($error['message'], self::trace($error['file'], $error['line']));
        }
    }

    private static function trace($errfile, $errline)
    {
        return "$errfile:$errline";
    }
}