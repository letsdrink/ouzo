<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests\Mock;

use PHPUnit\Framework\TestCase;

class AndArgumentMatcherTest extends TestCase
{
    /**
     * @test
     */
    public function shouldMatchWhenAllMatch()
    {
        //given
        $matcher = Mock::all(
            Mock::argThat()->containsSubstring('a'),
            Mock::argThat()->containsSubstring('b')
        );

        //when
        $result = $matcher->matches('abc');

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldNotMatchWhenNotAllMatch()
    {
        //given
        $matcher = new AndArgumentMatcher([
            Mock::argThat()->containsSubstring('a'),
            Mock::argThat()->containsSubstring('d')
        ]);

        //when
        $result = $matcher->matches('abc');

        //then
        $this->assertFalse($result);
    }
}
