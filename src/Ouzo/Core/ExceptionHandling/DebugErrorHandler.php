<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugErrorHandler extends ErrorHandler
{
    public function handleError(int $errorNumber, string $errorString, string $errorFile, int $errorLine): void
    {
        $this->createWhoops()->handleError($errorNumber, $errorString, $errorFile, $errorLine);
    }

    public function handleShutdown(): void
    {
        $this->createWhoops()->handleShutdown();
    }

    private function createWhoops(): Run
    {
        error_reporting(E_ALL);
        $run = new Run();
        $run->pushHandler(new PrettyPageHandler());
        $run->pushHandler(new DebugErrorLogHandler());
        return $run;
    }
}
