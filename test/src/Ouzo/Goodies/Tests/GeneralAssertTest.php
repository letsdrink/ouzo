<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\CatchException;
use Ouzo\Tests\GeneralAssert;
use Ouzo\Tests\Mock\Mock;

class GeneralAssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnInstance()
    {
        // when
        $instance = GeneralAssert::that(0);

        // then
        $this->assertInstanceOf('Ouzo\Tests\GeneralAssert', $instance);
    }

    /**
     * @test
     */
    public function shouldBeInstanceOf()
    {
        // then
        GeneralAssert::that(new stdClass())->isInstanceOf('stdClass');
        GeneralAssert::that(Mock::create('stdClass'))->isInstanceOf('stdClass');
    }

    function notInstanceOf()
    {
        return array(
            array(array(), 'stdClass'),
            array(4, 'stdClass'),
            array(true, 'stdClass'),
            array(new Example(), 'stdClass'),
            array(new stdClass(), 'Example')
        );
    }

    /**
     * @test
     * @dataProvider notInstanceOf
     * @param $instance
     * @param string $name
     */
    public function shouldNotBeInstanceOf($instance, $name)
    {
        CatchException::when(GeneralAssert::that($instance))->isInstanceOf($name);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldBeNull()
    {
        GeneralAssert::that(null)->isNull();
    }

    /**
     * @test
     * @dataProvider notNull
     * @param $notNull
     */
    public function shouldBeNotNull($notNull)
    {
        GeneralAssert::that($notNull)->isNotNull();
    }

    function notNull()
    {
        return array(
            array(1),
            array(0),
            array('1'),
            array(''),
            array('0'),
            array(5.4),
            array('word'),
            array(true),
            array('true'),
            array('false'),
            array(array())
        );
    }

    function notEqualToNull()
    {
        return array(
            array(1),
            array('1'),
            array('0'),
            array(5.4),
            array('word'),
            array(true),
            array('true'),
            array('false'),
            array(array())
        );
    }

    /**
     * @test
     * @dataProvider notNull
     * @param $notNull
     */
    public function shouldNotBeNull($notNull)
    {
        CatchException::when(GeneralAssert::that($notNull))->isNull();

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldNotBeNotNull()
    {
        CatchException::when(GeneralAssert::that(null))->isNotNull();

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     * @dataProvider notNull
     * @param $notNull
     */
    public function shouldBeEqual($notNull)
    {
        GeneralAssert::that($notNull)->isEqualTo($notNull);
    }

    /**
     * @test
     * @dataProvider notEqualToNull
     * @param $notNull
     */
    public function shouldNotBeEqual($notNull)
    {
        CatchException::when(GeneralAssert::that(null))->isEqualTo($notNull);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }
}

class Example
{
}
