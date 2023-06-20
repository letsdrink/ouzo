<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\TestCase;

class MyClass
{
    public function someMethodThatThrowsException()
    {
        throw new Exception('Fatal error', 555);
    }

    public function someMethod()
    {
    }
}

class CatchExceptionTest extends TestCase
{
    #[Test]
    public function shouldCatchException()
    {
        // given
        $object = new MyClass();

        // when
        CatchException::when($object)->someMethodThatThrowsException();

        // then
        CatchException::assertThat()->isInstanceOf('Exception');
    }

    #[Test]
    public function shouldNotCatchException()
    {
        // given
        $object = new MyClass();

        // when
        CatchException::when($object)->someMethod();

        // then
        CatchException::assertThat()->notCaught();
    }

    #[Test]
    public function shouldCheckIsMessageContains()
    {
        //given
        $object = new MyClass();

        //when
        CatchException::when($object)->someMethodThatThrowsException();

        //then
        CatchException::assertThat()->hasMessage('Fatal error');
    }

    #[Test]
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

    #[Test]
    public function shouldCheckIsCodeEquals()
    {
        //given
        $object = new MyClass();

        //when
        CatchException::when($object)->someMethodThatThrowsException();

        //then
        CatchException::assertThat()->hasCode(555);
    }
}
