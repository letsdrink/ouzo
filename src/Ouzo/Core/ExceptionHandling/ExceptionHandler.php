<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Routing\RouterException;
use Ouzo\UserException;

class ExceptionHandler
{
    private static $errorHandled = false;
    public static $errorRenderer = null;

    public function handleException($exception)
    {
        if (!$this->runOuzoExceptionHandler($exception)) {
            $this->runDefaultHandler($exception);
        }
    }

    protected function runOuzoExceptionHandler($exception)
    {
        if ($exception instanceof UserException) {
            $this->renderUserError(OuzoExceptionData::forException(500, $exception));
            return true;
        } elseif ($exception instanceof RouterException) {
            $this->handleError(OuzoExceptionData::forException(404, $exception));
            return true;
        } elseif ($exception instanceof OuzoException) {
            $this->handleError($exception->asExceptionData());
            return true;
        }
        return false;
    }

    protected function runDefaultHandler($exception)
    {
        $this->handleError(OuzoExceptionData::forException(500, $exception));
    }

    public function handleExceptionData(OuzoExceptionData $exceptionData)
    {
        $this->handleError($exceptionData);
    }

    public static function lastErrorHandled()
    {
        return self::$errorHandled;
    }

    protected function handleError($exception)
    {
        $this->renderError($exception);
    }

    private function renderUserError($exception)
    {
        header("Contains-Error-Message: User");
        $this->renderError($exception, 'user_exception');
    }

    protected function renderError(OuzoExceptionData $exceptionData, $viewName = 'exception')
    {
        try {
            ExceptionLogger::newInstance($exceptionData)->log();
            $renderer = self::$errorRenderer ?: new ErrorRenderer();
            $renderer->render($exceptionData, $viewName);
            self::$errorHandled = true;
        } catch (Exception $e) {
            echo "Framework critical error. Exception thrown in exception handler.<br>\n";
            echo "<hr>\n";
            echo "Message: " . $e->getMessage() . "<br>\n";
            echo "Trace: " . $e->getTraceAsString() . "<br>\n";
        }
    }
}
