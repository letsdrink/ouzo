<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\BooleanAssert;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class BooleanAssertTest extends TestCase
{
    #[Test]
    public function shouldReturnInstance()
    {
        // when
        $instance = BooleanAssert::that(0);

        // then
        $this->assertInstanceOf(BooleanAssert::class, $instance);
    }

    #[Test]
    public function shouldBeTrue()
    {
        // then
        BooleanAssert::that(true)->isTrue();
    }

    #[Test]
    public function shouldBeFalse()
    {
        // then
        BooleanAssert::that(false)->isFalse();
    }

    function notTrue()
    {
        return [
            [false],
            [1],
            [0],
            [''],
            ['0'],
            ['1'],
            ['t'],
            ['aa'],
            [new stdClass()],
            [[]],
            [null],
        ];
    }

    function notFalse()
    {
        return [
            [true],
            [1],
            [0],
            [''],
            ['0'],
            ['1'],
            ['t'],
            ['aa'],
            [new stdClass()],
            [[]],
            [null],
        ];
    }

    /**
     * @test
     * @dataProvider notTrue
     * @param $notTrue
     * @throws Exception
     */
    public function shouldNotBeTrue($notTrue)
    {
        CatchException::when(BooleanAssert::that($notTrue))->isTrue();

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    /**
     * @test
     * @dataProvider notFalse
     * @param $notFalse
     * @throws Exception
     */
    public function shouldNotBeFalse($notFalse)
    {
        CatchException::when(BooleanAssert::that($notFalse))->isFalse();

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }
}
