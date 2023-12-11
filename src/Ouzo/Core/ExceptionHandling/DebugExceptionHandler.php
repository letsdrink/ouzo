<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Http\MediaType;
use Ouzo\Response\ResponseTypeResolve;
use Ouzo\Uri;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class DebugExceptionHandler extends ExceptionHandler
{
    public function runDefaultHandler($exception): void
    {
        if ($this->isPrettyHandlerNeeded()) {
            $run = new Run();
            $run->pushHandler(new PrettyPageHandler());
            $run->pushHandler(new DebugErrorLogHandler());
            $run->handleException($exception);
        } else {
            $this->handleError(OuzoExceptionData::forException(500, $exception));
        }
    }

    private function isPrettyHandlerNeeded(): bool
    {
        $isHtmlResponse = ResponseTypeResolve::resolve() === MediaType::TEXT_HTML;
        return $isHtmlResponse && !Uri::isAjax();
    }
}
