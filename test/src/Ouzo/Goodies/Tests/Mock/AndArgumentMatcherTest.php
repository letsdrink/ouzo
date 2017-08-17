<?php

namespace Ouzo\Tests\Mock;


class AndArgumentMatcherTest extends \PHPUnit_Framework_TestCase
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
