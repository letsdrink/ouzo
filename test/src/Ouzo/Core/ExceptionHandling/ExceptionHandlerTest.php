<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    #[Test]
    public function shouldHandleException()
    {
        //given
        $exception = new \Exception("Some exception");
        ExceptionHandler::$errorRenderer = Mock::mock(ErrorRenderer::class);
        $handler = new ExceptionHandler();

        //when
        CatchException::when($handler)->handleException($exception);

        //then
        CatchException::assertThat()->notCaught();
        Mock::verify(ExceptionHandler::$errorRenderer)->render(OuzoExceptionData::forException(500, $exception), "exception");
    }
}
