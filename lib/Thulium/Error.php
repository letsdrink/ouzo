<?php
namespace Thulium;

class Error
{
    static public function exceptionHandler(\Exception $exception)
    {
        self::_handleError($exception->getMessage(), $exception->getTraceAsString());
    }

    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        throw new \Exception("$errfile:$errline - $errstr ERRNO($errno)");
    }

    private static function _clearOutputBuffers()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }

    private static function _handleError($errorMessage, $errorTrace)
    {
        Logger::getPanelLogger()->addError($errorMessage, array($errorTrace));
        /** @noinspection PhpIncludeInspection */
        self::_clearOutputBuffers();
        header("HTTP/1.1 500 Internal Server Error");
        require_once(ROOT_PATH . 'application' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'exception.phtml');
    }

    static public function shutdownHandler()
    {
        $error = error_get_last();

        if ($error && $error['type'] & (E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR)) {
            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }
}