<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Exception;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    #[Test]
    public function shouldHandleException()
    {
        //given
        $exception = new Exception('Some exception');
        /** @var Renderer|MockInterface $renderer */
        $renderer = Mock::create(ErrorRenderer::class);
        $handler = new ExceptionHandler($renderer);

        //when
        CatchException::when($handler)->handleException($exception);

        //then
        CatchException::assertThat()->notCaught();
        Mock::verify($renderer)->render(OuzoExceptionData::forException(500, $exception), 'exception');
    }
}
