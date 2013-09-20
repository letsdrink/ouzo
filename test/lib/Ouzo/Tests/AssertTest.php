<?php
namespace Ouzo\Tests;

use PHPUnit_Framework_ExpectationFailedException;
use PHPUnit_Framework_TestCase;

class AssertTest extends PHPUnit_Framework_TestCase {

    private function _assertNotContains() {
        $args = func_get_args();
        $array = array_shift($args);

        $object = CatchException::when(Assert::that($array));
        call_user_func_array(array($object, 'contains'), $args);
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsShouldAssertThatArrayContainsElement()
    {
        Assert::that(array('1'))->contains('1');
        Assert::that(array('1', '2'))->contains('1');
        Assert::that(array('1', '2', '3'))->contains('1');
        Assert::that(array('1', '2', '3'))->contains('1', '2');
        Assert::that(array('1', '2', '3'))->contains('1', '2', '3');
    }

    /**
     * @test
     */
    public function containsShouldThrowException()
    {
        $this->_assertNotContains(array(null), '1');
        $this->_assertNotContains(array('string'), '1');
        $this->_assertNotContains(array(array('1', '2')), '3');
        $this->_assertNotContains(array(array('1', '2')), '1', '3');
        $this->_assertNotContains(array(array('1', '2')), '1', '2', '3');
    }

    /**
     * @test
     */
    public function isEmptyShouldAssertThatArrayHasNoElements()
    {
        Assert::that(array())->isEmpty();
    }

    /**
     * @test
     */
    public function isEmptyShouldThrowException()
    {
        CatchException::when(Assert::that(array('1')))->isEmpty();
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->isEmpty();
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function isNotEmptyShouldAssertThatArrayHasElements()
    {
        Assert::that(array('1'))->isNotEmpty();
        Assert::that(array('1', '2'))->isNotEmpty();
    }

    /**
     * @test
     */
    public function isNotEmptyShouldThrowException()
    {
        CatchException::when(Assert::that(array()))->isNotEmpty();
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsOnlyShouldAssertThatArrayContainsElement()
    {
        Assert::that(array('1'))->containsOnly('1');
        Assert::that(array('1', '2', '3'))->containsOnly('1', '2', '3');
        Assert::that(array('1', '2', '3'))->containsOnly('3', '1', '2');
    }

    /**
     * @test
     */
    public function containsOnlyShouldThrowException()
    {
        CatchException::when(Assert::that(array(null)))->contains('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('string')))->containsOnly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsOnly('3');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsOnly('1', '3');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsOnly('1', '2', '3');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsOnly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2', '3')))->containsOnly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2', '3')))->containsOnly('1', '2');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function containsExactlyShouldAssertThatArrayContainsElementInGivenOrder()
    {
        Assert::that(array('1'))->containsExactly('1');
        Assert::that(array('1', '2', '3'))->containsExactly('1', '2', '3');
    }

    /**
     * @test
     */
    public function containsExactlyShouldThrowException()
    {
        CatchException::when(Assert::that(array(null)))->containsExactly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('string')))->containsExactly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsExactly('3');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsExactly('1', '3');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsExactly('1', '2', '3');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2')))->containsExactly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2', '3')))->containsExactly('1');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2', '3')))->containsExactly('1', '2');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');

        CatchException::when(Assert::that(array('1', '2', '3')))->containsExactly('3', '1', '2');
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }
}