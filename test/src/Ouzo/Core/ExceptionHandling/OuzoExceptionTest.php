<?php

namespace Ouzo\ExceptionHandling;

use Ouzo\Tests\Assert;

class OuzoExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnErrorMessages()
    {
        //given
        $ouzoException = new OuzoException(500, "TestException", [new Error(0, "Error 1"), new Error(1, "Error 2")]);

        //when
        $messages = $ouzoException->getErrorMessages();

        //then
        Assert::thatArray($messages)
            ->containsOnly("Error 1", "Error 2");
    }
}
