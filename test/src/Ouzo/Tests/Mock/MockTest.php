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

}
 