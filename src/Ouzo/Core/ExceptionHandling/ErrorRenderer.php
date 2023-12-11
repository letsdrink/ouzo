<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Response\ResponseTypeResolve;
use Ouzo\ViewPathResolver;

class ErrorRenderer implements Renderer
{
    public function render(OuzoExceptionData $exceptionData, ?string $viewName): void
    {
        $errorMessage = $exceptionData->getMessage();
        $errorTrace = $exceptionData->getStackTrace()->getTraceAsString();

        $this->clearOutputBuffers();
        header($exceptionData->getHeader());
        $responseType = ResponseTypeResolve::resolve();
        header("Content-type: {$responseType}");

        $additionalHeaders = $exceptionData->getAdditionalHeaders();
        array_walk($additionalHeaders, function ($header) {
            header($header);
        });

        require(ViewPathResolver::resolveViewPath($viewName, $responseType));
    }

    private function clearOutputBuffers(): void
    {
        while (ob_get_level()) {
            if (!ob_end_clean()) {
                break;
            }
        }
    }
}
