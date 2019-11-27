<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Loop\Loop;
use PHPUnit\Framework\TestCase;

class LoopTest extends TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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
