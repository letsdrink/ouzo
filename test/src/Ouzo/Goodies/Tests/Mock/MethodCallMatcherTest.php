<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Mock\AnyArgument;
use Ouzo\Tests\Mock\AnyArgumentList;
use Ouzo\Tests\Mock\MethodCall;
use Ouzo\Tests\Mock\MethodCallMatcher;

class TestClass
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

class MethodCallMatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldMatchExactArguments()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array(1, 2, '3'));

        //when
        $result = $matcher->matches(new MethodCall('fun', array(1, 2, '3')));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldMatchExactObjectArguments()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array(new TestClass('value')));

        //when
        $result = $matcher->matches(new MethodCall('fun', array(new TestClass('value'))));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldFailIfDifferentMethod()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array());

        //when
        $result = $matcher->matches(new MethodCall('other', array()));

        //then
        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function shouldMatchAnyArgument()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array(1, new AnyArgument(), "1"));

        //when
        $result = $matcher->matches(new MethodCall('fun', array(1, "any", "1")));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldMatchAnyArgumentList()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array(new AnyArgumentList()));

        //when
        $result = $matcher->matches(new MethodCall('fun', array(1, "1")));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldMatchNoArgumentsForAnyArgumentList()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array(new AnyArgumentList()));

        //when
        $result = $matcher->matches(new MethodCall('fun', array()));

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldNotMatchDifferentExactArguments()
    {
        //given
        $matcher = new MethodCallMatcher('fun', array(1, new AnyArgument(), "1"));

        //when
        $result = $matcher->matches(new MethodCall('fun', array(1, "any", "other")));

        //then
        $this->assertFalse($result);
    }
}
