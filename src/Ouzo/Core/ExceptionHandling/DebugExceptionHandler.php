<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Response\ResponseTypeResolve;
use Ouzo\Uri;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugExceptionHandler extends ExceptionHandler
{
    public function runDefaultHandler($exception)
    {
        if ($this->needPrettyHandler()) {
            $run = new Run();
            $run->pushHandler(new PrettyPageHandler());
            $run->pushHandler(new DebugErrorLogHandler());
            $run->handleException($exception);
        } else {
            $this->handleError(OuzoExceptionData::forException(500, $exception));
        }
    }

    private function needPrettyHandler(): bool
    {
        $isHtmlResponse = ResponseTypeResolve::resolve() == "text/html";
        return $isHtmlResponse && !Uri::isAjax();
    }
}
