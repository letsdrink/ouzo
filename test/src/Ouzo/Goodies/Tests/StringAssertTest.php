<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;

class StringAssertTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFailIfStringDoesNotMatchRegex()
    {
        CatchException::when(Assert::thatString("Frodo"))->matches('/Fro\d+/');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldMatchRegex()
    {
        Assert::thatString("Frodo12")->matches('/Fro\w+\d+/');
    }

    /**
     * @test
     */
    public function shouldBeEqualIgnoringCase()
    {
        Assert::thatString("Frodo")->isEqualToIgnoringCase('frodo');
    }

    /**
     * @test
     */
    public function shouldNotBeEqualIgnoringCase()
    {
        CatchException::when(Assert::thatString("Frodo12"))->isEqualToIgnoringCase('frodo');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldContainSubstring()
    {
        Assert::thatString("Frodo")->contains('od');
    }

    /**
     * @test
     */
    public function shouldFailIfDoesNotContainSubstring()
    {
        CatchException::when(Assert::thatString("Frodo"))->contains('invalid');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldStartWithPrefix()
    {
        Assert::thatString("Frodo")->startsWith('Fr');
    }

    /**
     * @test
     */
    public function shouldFailIfDoesNotStartWithPrefix()
    {
        CatchException::when(Assert::thatString("Frodo"))->startsWith('invalid');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldEndWithPrefix()
    {
        Assert::thatString("Frodo")->endsWith('do');
    }

    /**
     * @test
     */
    public function shouldFailIfDoesNotEndWithPrefix()
    {
        CatchException::when(Assert::thatString("Frodo"))->endsWith('invalid');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldAcceptCorrectSize()
    {
        Assert::thatString("Frodo")->hasSize(5);
    }

    /**
     * @test
     */
    public function shouldFailIfDifferentSize()
    {
        CatchException::when(Assert::thatString("Frodo"))->hasSize(7);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldAssertThatDoesNotContainSubstring()
    {
        Assert::thatString("Frodo")->doesNotContain('asd');
    }

    /**
     * @test
     */
    public function shouldFailForAssertThatDoesNotContainSubstring()
    {
        CatchException::when(Assert::thatString("Frodo"))->doesNotContain('od');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldAssertThatStringsAreEqual()
    {
        Assert::thatString("Frodo")->isEqualTo('Frodo');
    }

    /**
     * @test
     */
    public function shouldFailIfStringsAreNotEqual()
    {
        CatchException::when(Assert::thatString("Frodo"))->isEqualTo('od');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldAssertThatStringsAreNotEqual()
    {
        Assert::thatString("Frodo")->isNotEqualTo('asd');
    }

    /**
     * @test
     */
    public function shouldFailIfStringsAreEqualWhenTheyShouldNotBe()
    {
        CatchException::when(Assert::thatString("Frodo"))->isNotEqualTo('Frodo');

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldCheckIsStringIsNull()
    {
        Assert::thatString(null)->isNull();
    }

    /**
     * @test
     */
    public function shouldCheckIsStringIsNotNull()
    {
        Assert::thatString('Floki')->isNotNull();
    }

    /**
     * @test
     */
    public function shouldCheckIsStringIsEmpty()
    {
        Assert::thatString('')->isEmpty();
    }

    /**
     * @test
     */
    public function shouldCheckIsStringIsNotEmpty()
    {
        Assert::thatString('Lady Stoneheart')->isNotEmpty();
    }
}
