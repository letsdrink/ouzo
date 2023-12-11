<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Config;
use Ouzo\Routing\RouterException;
use Ouzo\UserException;

class ExceptionHandler
{
    private bool $errorHandled = false;
    private bool $isCli;
    private Renderer $errorRenderer;

    public function __construct(?Renderer $errorRenderer = null)
    {
        global $argv;
        $this->isCli = isset($argv[0]);
        $this->errorRenderer = is_null($errorRenderer) ? ($this->isCli ? new CliErrorRenderer() : new ErrorRenderer()) : $errorRenderer;
    }

    public function handleException($exception): void
    {
        if (!$this->runOuzoExceptionHandler($exception)) {
            $this->runDefaultHandler($exception);
        }
    }

    protected function runOuzoExceptionHandler($exception): bool
    {
        if ($exception instanceof UserException) {
            $this->renderUserError(OuzoExceptionData::forException(500, $exception));
            return true;
        }
        if ($exception instanceof RouterException) {
            $this->handleError(OuzoExceptionData::forException(404, $exception));
            return true;
        }
        if ($exception instanceof OuzoException) {
            $this->handleError($exception->asExceptionData());
            return true;
        }
        return false;
    }

    protected function runDefaultHandler($exception): void
    {
        $this->handleError(OuzoExceptionData::forException(500, $exception));
    }

    public function handleExceptionData(OuzoExceptionData $exceptionData): void
    {
        $this->handleError($exceptionData);
    }

    public function lastErrorHandled(): bool
    {
        return $this->errorHandled;
    }

    protected function handleError($exception): void
    {
        $this->renderError($exception);
    }

    private function renderUserError($exception): void
    {
        if (!$this->isCli) {
            header('Contains-Error-Message: User');
        }
        $this->renderError($exception, 'user_exception');
    }

    protected function renderError(OuzoExceptionData $exceptionData, $viewName = 'exception'): void
    {
        try {
            ExceptionLogger::newInstance($exceptionData)->log();
            $this->errorRenderer->render($exceptionData, $viewName);
            $this->errorHandled = true;
        } catch (Exception $e) {
            echo "Framework critical error. Exception thrown in exception handler.<br>\n";
            ExceptionLogger::forException($e)->log();
            if (Config::getValue('debug')) {
                echo "<hr>\n";
                echo "Message: " . $e->getMessage() . "<br>\n";
                echo "Trace: " . $e->getTraceAsString() . "<br>\n";
            }
        }
    }
}
