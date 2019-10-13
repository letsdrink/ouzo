<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\CatchException;
use Ouzo\Tests\GeneralAssert;
use Ouzo\Tests\Mock\Mock;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class GeneralAssertTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnInstance()
    {
        // when
        $instance = GeneralAssert::that(0);

        // then
        $this->assertInstanceOf(GeneralAssert::class, $instance);
    }

    /**
     * @test
     */
    public function shouldBeInstanceOf()
    {
        // then
        GeneralAssert::that(new stdClass())->isInstanceOf(stdClass::class);
        GeneralAssert::that(Mock::create(stdClass::class))->isInstanceOf(stdClass::class);
    }

    function notInstanceOf()
    {
        return [
            [[], stdClass::class],
            [4, stdClass::class],
            [true, stdClass::class],
            [new Example(), stdClass::class],
            [new stdClass(), Example::class]
        ];
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

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
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
        return [
            [1],
            [0],
            ['1'],
            [''],
            ['0'],
            [5.4],
            ['word'],
            [true],
            ['true'],
            ['false'],
            [[]]
        ];
    }

    function notEqualToNull()
    {
        return [
            [1],
            ['1'],
            ['0'],
            [5.4],
            ['word'],
            [true],
            ['true'],
            ['false'],
            [[]]
        ];
    }

    /**
     * @test
     * @dataProvider notNull
     * @param $notNull
     */
    public function shouldNotBeNull($notNull)
    {
        CatchException::when(GeneralAssert::that($notNull))->isNull();

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    /**
     * @test
     */
    public function shouldNotBeNotNull()
    {
        CatchException::when(GeneralAssert::that(null))->isNotNull();

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
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

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }
}

class Example
{
}
