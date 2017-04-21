<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Response\ResponseTypeResolve;
use Ouzo\ViewPathResolver;

class ErrorRenderer
{
    public function render(OuzoExceptionData $exceptionData, $viewName)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $errorMessage = $exceptionData->getMessage();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $errorTrace = $exceptionData->getStackTrace()->getTraceAsString();

        $this->clearOutputBuffers();
        header($exceptionData->getHeader());
        $responseType = ResponseTypeResolve::resolve();
        header('Content-type: ' . $responseType);

        $additionalHeaders = $exceptionData->getAdditionalHeaders();
        array_walk($additionalHeaders, function ($header) {
            header($header);
        });

        /** @noinspection PhpIncludeInspection */
        require(ViewPathResolver::resolveViewPath($viewName, $responseType));
    }

    private function clearOutputBuffers()
    {
        while (ob_get_level()) {
            if (!ob_end_clean()) {
                break;
            }
        }
    }
}
