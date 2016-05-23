<?php
namespace Ouzo\ExceptionHandling;

use Ouzo\PageNotFoundException;
use Ouzo\Tests\Mock\Mock;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRender404OnRouterException()
    {
        //given
        $pageNotFoundException = new PageNotFoundException();
        ExceptionHandler::$errorRenderer = Mock::mock('Ouzo\ExceptionHandling\ErrorRenderer');

        //when
        ErrorHandler::exceptionHandler($pageNotFoundException);

        //then
        Mock::verify(ExceptionHandler::$errorRenderer)->render(Mock::any(), "404");
    }
}
