<?php

namespace Ouzo\ExceptionHandling;

interface Renderer
{
    function render(OuzoExceptionData $exceptionData, ?string $viewName): void;
}