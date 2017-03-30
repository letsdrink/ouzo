<?php
namespace Ouzo\ExceptionHandling;

use Ouzo\Logger\Logger;
use Ouzo\Response\ResponseTypeResolve;
use Ouzo\Utilities\Objects;
use Ouzo\ViewPathResolver;

class ErrorRenderer
{
    public function render(OuzoExceptionData $exceptionData, $viewName)
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $errorMessage = $exceptionData->getMessage();
        $errorTrace = $exceptionData->getStackTrace();
        Logger::getLogger(__CLASS__)->error($exceptionData->getOriginalMessage());
        Logger::getLogger(__CLASS__)->error(Objects::toString($errorTrace));
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
