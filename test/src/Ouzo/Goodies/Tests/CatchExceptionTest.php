<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\CatchException;

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
        CatchException::assertThat()->hasMessage('Fatal error');
    }

    /**
     * @test
     */
    public function getShouldReturnException()
    {
        // given
        $object = new MyClass();
        CatchException::when($object)->someMethodThatThrowsException();

        // when
        $exception = CatchException::get();

        // then
        $this->assertInstanceOf('Exception', $exception);
    }
}
