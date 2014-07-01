<?php
namespace Ouzo\Tests;

use Exception;
use PHPUnit_Framework_TestCase;

class MyClass
{
    public function someMethodThatThrowsException()
    {
        throw new Exception('Fatal error');
    }

    public function someMethod()
    {
    }
}

class CatchExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCatchException()
    {
        // given
        $object = new MyClass();

        // when
        CatchException::when($object)->someMethodThatThrowsException();

        // then
        CatchException::assertThat()->isInstanceOf('Exception');
    }

    /**
     * @test
     */
    public function shouldNotCatchException()
    {
        // given
        $object = new MyClass();

        // when
        CatchException::when($object)->someMethod();

        // then
        CatchException::assertThat()->notCaught();
    }

    /**
     * @test
     */
    public function shouldCheckIsMessageContains()
    {
        //given
        $object = new MyClass();

        //when
        CatchException::when($object)->someMethodThatThrowsException();

        //then
        CatchException::assertThat()->equalMessage('Fatal error');
    }
}