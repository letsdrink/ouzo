<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Loop\Loop;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class LoopTest extends TestCase
{
    #[Test]
    public function shouldRunLoopWithForEach()
    {
        // given
        $counter = 0;

        // when
        Loop::of(10)
            ->withFixedDelay(0)
            ->forEach(function () use (&$counter) {
                $counter++;
            })->run();

        // then
        $this->assertEquals(10, $counter);
    }

    #[Test]
    public function shouldRunLoopWithForEveryNth()
    {
        // given
        $counter = 0;

        // when
        Loop::of(10)
            ->withFixedDelay(0)
            ->forEveryNth(5, function () use (&$counter) {
                $counter++;
            })->run();

        // then
        $this->assertEquals(2, $counter);
    }

    #[Test]
    public function shouldRunLoopWithForMultipleEveryNth()
    {
        // given
        $counter = 0;

        // when
        Loop::of(10)
            ->withFixedDelay(0)
            ->forEveryNth(1, function () use (&$counter) {
                $counter++;
            })
            ->forEveryNth(5, function () use (&$counter) {
                $counter++;
            })->run();

        // then
        $this->assertEquals(10 + 2, $counter);
    }

    #[Test]
    public function shouldRunLoopWithForEachAndForEveryNth()
    {
        // given
        $counter = 0;

        // when
        Loop::of(10)
            ->withFixedDelay(0)
            ->forEach(function () use (&$counter) {
                $counter++;
            })
            ->forEveryNth(5, function () use (&$counter) {
                $counter++;
            })->run();

        // then
        $this->assertEquals(10 + 2, $counter);
    }
}
