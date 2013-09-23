<?php
namespace Ouzo\Tests;

use Ouzo\Utilities\Objects;
use PHPUnit_Framework_ExpectationFailedException;
use PHPUnit_Framework_TestCase;

class AssertTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function containsShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(array('1'))->contains('1');
        Assert::thatArray(array('1', '2'))->contains('1');
        Assert::thatArray(array('1', '2', '3'))->contains('1');
        Assert::thatArray(array('1', '2', '3'))->contains('1', '2');
        Assert::thatArray(array('1', '2', '3'))->contains('1', '2', '3');
        Assert::thatArray(array('1', '2', '3'))->contains('3', '2', '1');
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
    public function hasSizeShouldAssertThatArrayHasSpecifiedSize()
    {
        Assert::thatArray(array())->hasSize(0);
        Assert::thatArray(array('1'))->hasSize(1);
        Assert::thatArray(array('1', '2'))->hasSize(2);
    }

    /**
     * @test
     */
    public function hasSizeShouldThrowException()
    {
        $this->_assertNotHasSize(array(), 1);
        $this->_assertNotHasSize(array('1'), 2);
        $this->_assertNotHasSize(array('1', '2'), 0);
    }

    /**
     * @test
     */
    public function isEmptyShouldAssertThatArrayHasNoElements()
    {
        Assert::thatArray(array())->isEmpty();
    }

    /**
     * @test
     */
    public function isEmptyShouldThrowException()
    {
        $this->_assertNotIsEmpty(array('1'));
        $this->_assertNotIsEmpty(array('1', '2'));
    }

    /**
     * @test
     */
    public function isNotEmptyShouldAssertThatArrayHasElements()
    {
        Assert::thatArray(array('1'))->isNotEmpty();
        Assert::thatArray(array('1', '2'))->isNotEmpty();
    }

    /**
     * @test
     */
    public function isNotEmptyShouldThrowException()
    {
        $this->_assertNotIsNotEmpty(array());
    }

    /**
     * @test
     */
    public function containsOnlyShouldAssertThatArrayContainsElement()
    {
        Assert::thatArray(array('1'))->containsOnly('1');
        Assert::thatArray(array('1', '2', '3'))->containsOnly('1', '2', '3');
        Assert::thatArray(array('1', '2', '3'))->containsOnly('3', '1', '2');
    }

    /**
     * @test
     */
    public function containsOnlyShouldThrowException()
    {
        $this->_assertNotContainsOnly(array(null), '1');
        $this->_assertNotContainsOnly(array('string'), '1');
        $this->_assertNotContainsOnly(array(array('1', '2')), '3');
        $this->_assertNotContainsOnly(array(array('1', '2')), '1', '3');
        $this->_assertNotContainsOnly(array(array('1', '2')), '1', '2', '3');
        $this->_assertNotContainsOnly(array(array('1', '2')), '1');
        $this->_assertNotContainsOnly(array(array('1', '2', '3')), '1');
        $this->_assertNotContainsOnly(array(array('1', '2', '3')), '1', '2');
    }

    /**
     * @test
     */
    public function containsExactlyShouldAssertThatArrayContainsElementInGivenOrder()
    {
        Assert::thatArray(array('1'))->containsExactly('1');
        Assert::thatArray(array('1', '2', '3'))->containsExactly('1', '2', '3');
    }

    /**
     * @test
     */
    public function containsExactlyShouldThrowException()
    {
        $this->_assertNotContainsExactly(array(null), '1');
        $this->_assertNotContainsExactly(array('string'), '1');
        $this->_assertNotContainsExactly(array(array('1', '2')), '3');
        $this->_assertNotContainsExactly(array(array('1', '2')), '1', '3');
        $this->_assertNotContainsExactly(array(array('1', '2')), '1', '2', '3');
        $this->_assertNotContainsExactly(array(array('1', '2')), '1');
        $this->_assertNotContainsExactly(array(array('1', '2', '3')), '1');
        $this->_assertNotContainsExactly(array(array('1', '2', '3')), '1', '2');
        $this->_assertNotContainsExactly(array(array('1', '2', '3')), '3', '1', '2');
    }

    private function _assertNot()
    {
        $args = func_get_args();
        $method = array_shift($args);
        $array = array_shift($args);

        call_user_func_array(array(CatchException::when(Assert::thatArray($array)), $method), $args);
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    private function _assertNotContains()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('contains'), func_get_args()));
    }

    private function _assertNotContainsOnly()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('containsOnly'), func_get_args()));
    }

    private function _assertNotContainsExactly()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('containsExactly'), func_get_args()));
    }

    private function _assertNotIsEmpty()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('isEmpty'), func_get_args()));
    }

    private function _assertNotIsNotEmpty()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('isNotEmpty'), func_get_args()));
    }

    private function _assertNotHasSize()
    {
        call_user_func_array(array($this, '_assertNot'), array_merge(array('hasSize'), func_get_args()));
    }
}