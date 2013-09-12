<?php
namespace Ouzo;

class Error
{
    static public function exceptionHandler(\Exception $exception)
    {
        self::_handleError($exception->getMessage(), $exception->getTraceAsString());
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
        try {
            Logger::getPanelLogger()->addError($errorMessage, array($errorTrace));
            /** @noinspection PhpIncludeInspection */
            self::_clearOutputBuffers();
            header("HTTP/1.1 500 Internal Server Error");

            require_once(ViewPathResolver::resolveViewPath('exception'));
        } catch (\Exception $e) {
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