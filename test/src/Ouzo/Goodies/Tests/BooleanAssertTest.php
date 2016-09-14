<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\BooleanAssert;
use Ouzo\Tests\CatchException;

class BooleanAssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnInstance()
    {
        // when
        $instance = BooleanAssert::that(0);

        // then
        $this->assertInstanceOf('Ouzo\Tests\BooleanAssert', $instance);
    }

    /**
     * @test
     */
    public function shouldBeTrue()
    {
        // then
        BooleanAssert::that(true)->isTrue();
    }

    /**
     * @test
     */
    public function shouldBeFalse()
    {
        // then
        BooleanAssert::that(false)->isFalse();
    }

    function notTrue()
    {
        return array(
            array(false),
            array(1),
            array(0),
            array(''),
            array('0'),
            array('1'),
            array('t'),
            array('aa'),
            array(new stdClass()),
            array(array()),
            array(null),
        );
    }

    function notFalse()
    {
        return array(
            array(true),
            array(1),
            array(0),
            array(''),
            array('0'),
            array('1'),
            array('t'),
            array('aa'),
            array(new stdClass()),
            array(array()),
            array(null),
        );
    }

    /**
     * @test
     * @dataProvider notTrue
     * @param $notTrue
     */
    public function shouldNotBeTrue($notTrue)
    {
        CatchException::when(BooleanAssert::that($notTrue))->isTrue();

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     * @dataProvider notFalse
     * @param $notFalse
     */
    public function shouldNotBeFalse($notFalse)
    {
        CatchException::when(BooleanAssert::that($notFalse))->isFalse();

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }
}
