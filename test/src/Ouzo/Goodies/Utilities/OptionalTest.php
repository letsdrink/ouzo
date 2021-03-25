<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\FluentFunctions;
use Ouzo\Utilities\Optional;

class MyOptionalClass
{
    public $myField = 'abc';

    public function myMethod()
    {
        return '123';
    }
}

use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
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
        Assert::thatBool($present)->isTrue();
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
        Assert::that($result)->isInstanceOf(Optional::class);
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
        CatchException::assertThat()->isInstanceOf(Exception::class);
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
        Assert::that($result)->isInstanceOf(Optional::class);
        Assert::that($result->orNull())->isNull();
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
    public function orShouldReturnValueOnNullWhenGettingFieldValue()
    {
        //given
        $optional = Optional::fromNullable(null);

        //when
        $result = $optional->field->or('456');

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
        Assert::that($optional)->isInstanceOf(Optional::class);
    }

    /**
     * @test
     */
    public function ofShouldThrowExceptionOnNull()
    {
        $this->expectException(InvalidArgumentException::class);

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
        Assert::that($result)->isInstanceOf(Optional::class);
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
        Assert::that($result)->isInstanceOf(Optional::class);
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

    /**
     * @test
     */
    public function shouldMapObject()
    {
        //given
        $optional = Optional::fromNullable(new MyOptionalClass());
        $closure = FluentFunctions::extractField('myField')->append('!!!');

        //when
        $result = $optional->map($closure)->get();

        //then
        $this->assertEquals('abc!!!', $result);
    }

    /**
     * @test
     */
    public function shouldReturnOrForAbsentMap()
    {
        //given
        $optional = Optional::fromNullable(null);
        $closure = FluentFunctions::extractField('myField')->append('!!!');

        //when
        $result = $optional->map($closure)->or('def');

        //then
        $this->assertEquals('def', $result);
    }

    /**
     * @test
     */
    public function shouldFlattenValue()
    {
        //given
        $optional = Optional::of(Optional::of(Optional::of(1)));

        //when
        $result = $optional->flatten()->get();

        //then
        $this->assertEquals(1, $result);
    }

    /**
     * @test
     */
    public function shouldFlattenNullValue()
    {
        //given
        $optional = Optional::of(Optional::of(Optional::fromNullable(null)));

        //when
        $result = $optional->flatten()->orNull();

        //then
        Assert::that($result)->isNull();
    }
}
