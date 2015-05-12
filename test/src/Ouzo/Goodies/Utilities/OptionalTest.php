<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Optional;

class MyOptionalClass
{
    public $myField = 'abc';

    public function myMethod()
    {
        return '123';
    }
}

class OptionalTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function isPresentShouldReturnFalseForNull()
    {
        //given
        $optional = Optional::fromNullable(null);

        //when
        $present = $optional->isPresent();

        //then
        $this->assertFalse($present);
    }

    /**
     * @test
     */
    public function isPresentShouldReturnTrueForNotNull()
    {
        //given
        $optional = Optional::fromNullable(new stdClass());

        //when
        $present = $optional->isPresent();

        //then
        $this->assertTrue($present);
    }

    /**
     * @test
     */
    public function invokingMethodShouldReturnOptional()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->myMethod();

        //then
        $this->assertInstanceOf('\Ouzo\Utilities\Optional', $result);
    }

    /**
     * @test
     */
    public function getShouldReturnValue()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->myMethod()->get();

        //then
        $this->assertEquals('123', $result);
    }

    /**
     * @test
     */
    public function getShouldThrowExceptionOnNull()
    {
        //given
        $optional = Optional::fromNullable(null);

        //when
        CatchException::when($optional)->get();

        //then
        CatchException::assertThat()->isInstanceOf('\Exception');
    }

    /**
     * @test
     */
    public function invokingNonExistentMethodShouldReturnOptionalWithNull()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->unknownMethod();

        //then
        $this->assertInstanceOf('\Ouzo\Utilities\Optional', $result);
        $this->assertNull($result->orNull());
    }

    /**
     * @test
     */
    public function orShouldReturnValueWhenNotNull()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->myMethod()->or('456');

        //then
        $this->assertEquals('123', $result);
    }

    /**
     * @test
     */
    public function orShouldReturnValueOnNull()
    {
        //given
        $optional = Optional::fromNullable(null);

        //when
        $result = $optional->or('456');

        //then
        $this->assertEquals('456', $result);
    }

    /**
     * @test
     */
    public function orNullShouldReturnValueWhenNotNull()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->myMethod()->orNull();

        //then
        $this->assertEquals('123', $result);
    }

    /**
     * @test
     */
    public function orNullShouldReturnNullOnNull()
    {
        //given
        $optional = Optional::fromNullable(null);

        //when
        $result = $optional->orNull();

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ofShouldCreateOptional()
    {
        //when
        $optional = Optional::of(new stdClass());

        //then
        $this->assertInstanceOf('\Ouzo\Utilities\Optional', $optional);
    }

    /**
     * @expectedException InvalidArgumentException
     * @test
     */
    public function ofShouldThrowExceptionOnNull()
    {
        Optional::of(null);
    }

    /**
     * @test
     */
    public function fieldAccessShouldReturnOptional()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->myField;

        //then
        $this->assertInstanceOf('\Ouzo\Utilities\Optional', $result);
    }

    /**
     * @test
     */
    public function fieldAccessOnNonExistentFieldShouldReturnOptional()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->unknownField;

        //then
        $this->assertInstanceOf('\Ouzo\Utilities\Optional', $result);
    }

    /**
     * @test
     */
    public function getOnFieldShouldReturnValue()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());

        //when
        $result = $optional->myField->get();

        //then
        $this->assertEquals('abc', $result);
    }
}
