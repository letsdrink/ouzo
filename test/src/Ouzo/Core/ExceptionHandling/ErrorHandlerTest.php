<?php

namespace Ouzo\ExceptionHandling;

use Ouzo\PageNotFoundException;
use Ouzo\Tests\Mock\Mock;

use PHPUnit\Framework\TestCase; 

class ErrorHandlerTest extends TestCase
{
    /**
     * @test
     */
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
