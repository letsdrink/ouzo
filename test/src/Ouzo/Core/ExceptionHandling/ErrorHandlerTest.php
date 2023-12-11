<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use Ouzo\PageNotFoundException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTest extends TestCase
{
    #[Test]
    public function shouldRender404OnRouterException()
    {
        //given
        $pageNotFoundException = new PageNotFoundException();
        /** @var Renderer|MockInterface $renderer */
        $renderer = Mock::create(ErrorRenderer::class);
        $handler = new ErrorHandler(new ExceptionHandler($renderer));

        //when
        $handler->handleException($pageNotFoundException);

        //then
        Mock::verify($renderer)->render(Mock::any(), 'exception');
    }
}
