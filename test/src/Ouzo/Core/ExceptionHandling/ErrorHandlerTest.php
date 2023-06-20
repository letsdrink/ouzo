<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\PageNotFoundException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    #[Test]
    public function shouldRender404OnRouterException()
    {
        //given
        $pageNotFoundException = new PageNotFoundException();
        ExceptionHandler::$errorRenderer = Mock::mock(ErrorRenderer::class);

        //when
        ErrorHandler::exceptionHandler($pageNotFoundException);

        //then
        Mock::verify(ExceptionHandler::$errorRenderer)->render(Mock::any(), "exception");
    }
}
