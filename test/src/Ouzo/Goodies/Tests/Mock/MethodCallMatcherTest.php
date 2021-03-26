<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Mock\AnyArgument;
use Ouzo\Tests\Mock\AnyArgumentList;
use Ouzo\Tests\Mock\MethodCall;
use Ouzo\Tests\Mock\MethodCallMatcher;
use PHPUnit\Framework\TestCase;

class TestClass
{
    public function __construct(private string $value)
    {
    }
}

class MethodCallMatcherTest extends TestCase
{
    /**
     * @test
     */
    public function shouldMatchExactArguments()
    {
        //given
        $matcher = new MethodCallMatcher('fun', [1, 2, '3']);

        //when
        $result = $matcher->matches(new MethodCall('fun', [1, 2, '3']));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldMatchExactObjectArguments()
    {
        //given
        $matcher = new MethodCallMatcher('fun', [new TestClass('value')]);

        //when
        $result = $matcher->matches(new MethodCall('fun', [new TestClass('value')]));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldFailIfDifferentMethod()
    {
        //given
        $matcher = new MethodCallMatcher('fun', []);

        //when
        $result = $matcher->matches(new MethodCall('other', []));

        //then
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function shouldMatchAnyArgument()
    {
        //given
        $matcher = new MethodCallMatcher('fun', [1, new AnyArgument(), "1"]);

        //when
        $result = $matcher->matches(new MethodCall('fun', [1, "any", "1"]));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldMatchAnyArgumentList()
    {
        //given
        $matcher = new MethodCallMatcher('fun', [new AnyArgumentList()]);

        //when
        $result = $matcher->matches(new MethodCall('fun', [1, "1"]));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldMatchNoArgumentsForAnyArgumentList()
    {
        //given
        $matcher = new MethodCallMatcher('fun', [new AnyArgumentList()]);

        //when
        $result = $matcher->matches(new MethodCall('fun', []));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldNotMatchDifferentExactArguments()
    {
        //given
        $matcher = new MethodCallMatcher('fun', [1, new AnyArgument(), "1"]);

        //when
        $result = $matcher->matches(new MethodCall('fun', [1, "any", "other"]));

        //then
        $this->assertFalse($result);
    }
}
