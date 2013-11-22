<?php

namespace Ouzo\Tests;

use Exception;
use Ouzo\Tests\Mock\Mock;
use PHPUnit_Framework_ExpectationFailedException;

class MockTestClass
{
    function method()
    {
    }

    function method2()
    {
    }
}

class MockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnMockObjectOfTheGivenType()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');

        //when
        $result = $mock instanceof MockTestClass;

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldStubMethod()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');
        Mock::when($mock)->method()->thenReturn('result');

        //when
        $result = $mock->method();

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldVerifyMethodCall()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');

        //when
        $mock->method("arg");


        //then
        Mock::verify($mock)->method("arg");
    }

    /**
     * @test
     */
    public function shouldFailIfMethodWasNotCalled()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');

        //when
        CatchException::when(Mock::verify($mock))->method();

        //then
        CatchException::assertThat()->isInstanceOf("PHPUnit_Framework_ExpectationFailedException");
    }

    /**
     * @test
     */
    public function shouldVerifyMethodIsNotCalled()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');

        //when
        $mock->method("arg");
        $mock->method2("arg");

        //then
        Mock::verify($mock)->neverReceived()->method("other");
    }

    /**
     * @test
     */
    public function shouldFailIfUnwantedMethodWasCalled()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');
        $mock->method(1);

        //when
        try {
            Mock::verify($mock)->neverReceived()->method("arg");
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1)', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(1) is never called', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldFailIfUnwantedMethodWasCalledWithAnyArguments()
    {
        //given
        $mock = Mock::mock();
        $mock->method(1);

        //when
        try {
            Mock::verify($mock)->neverReceived()->method(Mock::anyArgList());
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1)', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(any arguments) is never called', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldShowActualInteractions()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');
        $mock->method(1);
        $mock->method2(1);

        //when
        try {
            Mock::verify($mock)->method(1, 2);
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1), method2(1)', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(1, 2)', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldHandleArrayAndObjectParametersInActualInteractionsMessage()
    {
        //given
        $mock = Mock::mock();
        $mock->method(1, null, array(1, 2), new MockTestClass());

        //when
        try {
            Mock::verify($mock)->method(1, 2);
        } //then
        catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals('method(1, null, [1, 2], Ouzo\Tests\MockTestClass {})', $e->getComparisonFailure()->getActual());
            $this->assertEquals('method(1, 2)', $e->getComparisonFailure()->getExpected());
        }
    }

    /**
     * @test
     */
    public function shouldReturnSimpleMockIfNoClassGiven()
    {
        //given
        $mock = Mock::mock();
        Mock::when($mock)->method()->thenReturn('result');

        //when
        $result = $mock->method();

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldThrowException()
    {
        //given
        $mock = Mock::mock();
        $exception = new Exception("msg");
        Mock::when($mock)->method()->thenThrow($exception);

        //when
        CatchException::when($mock)->method();

        //then
        CatchException::assertThat()->isEqualTo($exception);
    }

    /**
     * @test
     */
    public function shouldVerifyThatMethodWasCalledWithAnyArgument()
    {
        //given
        $mock = Mock::mock();

        //when
        $mock->method("arg1", "arg2");


        //then
        Mock::verify($mock)->method(Mock::any(), "arg2");
    }

    /**
     * @test
     */
    public function shouldVerifyThatMethodWasCalledWithAnyArgumentList()
    {
        //given
        $mock = Mock::mock();

        //when
        $mock->method("arg1", "arg2");


        //then
        Mock::verify($mock)->method(Mock::anyArgList());
    }

    /**
     * @test
     */
    public function shouldStubMethodForAnyArgument()
    {
        //given
        $mock = Mock::mock('Ouzo\Tests\MockTestClass');

        Mock::when($mock)->method("first", Mock::any())->thenReturn('result');

        //when
        $result = $mock->method("first", "any");

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldStubMethodForAnyArgumentList()
    {
        //given
        $mock = Mock::mock();

        Mock::when($mock)->method(Mock::anyArgList())->thenReturn('result');

        //when
        $result = $mock->method(1, 2);

        //then
        $this->assertEquals("result", $result);
    }

    /**
     * @test
     */
    public function shouldReturnNullIfNoStubbedMethodMatchesTheCall()
    {
        //given
        $mock = Mock::mock();

        Mock::when($mock)->method(Mock::any(), 1)->thenReturn('result');

        //when
        $result = $mock->method(1, 2);

        //then
        $this->assertNull( $result);
    }

}
 