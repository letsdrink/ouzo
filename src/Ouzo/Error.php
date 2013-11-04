<?php
namespace Ouzo;

use Exception;
use Ouzo\Logger\Logger;
use Ouzo\Routing\RouterException;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Objects;

class Error
{
    static public function exceptionHandler(Exception $exception)
    {
        if ($exception instanceof RouterException) {
            self::_renderNotFoundError($exception->getMessage(), $exception->getTraceAsString());
        } else {
            self::_handleError($exception->getMessage(), $exception->getTraceAsString());
        }
    }

    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        self::_handleError("$errstr ERRNO($errno)", "$errfile:$errline");
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

    private static function _renderNotFoundError($errorMessage, $errorTrace)
    {
        self::_renderError($errorMessage, $errorTrace, "HTTP/1.0 404 Not Found", '404');
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