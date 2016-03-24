<?php
namespace Ouzo\ExceptionHandling;

use Ouzo\PageNotFoundException;
use Ouzo\Tests\Assert;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldRender404OnRouterException()
    {
        //given
        $pageNotFoundException = new PageNotFoundException();

        //when
        ErrorHandler::exceptionHandler($pageNotFoundException);

        //then
        Assert::thatArray(get_included_files())
            ->contains("404.phtml");
    }
}
