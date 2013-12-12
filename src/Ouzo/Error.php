<?php
namespace Ouzo;

use ErrorException;
use Exception;
use Ouzo\Logger\Logger;
use Ouzo\Routing\RouterException;
use Ouzo\Utilities\Objects;

class Error
{
    public static function exceptionHandler(Exception $exception)
    {
        self::_handleError($exception->getMessage(), $exception->getTraceAsString());
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
            self::_handleError($errstr, $errfile);
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

    private static function _renderError($errorMessage, $errorTrace, $header, $viewName)
    {
        try {
            Logger::getLogger(__CLASS__)->error($errorMessage);
            Logger::getLogger(__CLASS__)->error(Objects::toString($errorTrace));
            /** @noinspection PhpIncludeInspection */
            self::_clearOutputBuffers();
            header($header);

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

        if ($error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}