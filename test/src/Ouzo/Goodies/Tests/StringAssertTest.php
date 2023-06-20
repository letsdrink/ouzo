<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class StringAssertTest extends TestCase
{
    #[Test]
    public function shouldFailIfStringDoesNotMatchRegex()
    {
        CatchException::when(Assert::thatString("Frodo"))->matches('/Fro\d+/');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldMatchRegex()
    {
        Assert::thatString("Frodo12")->matches('/Fro\w+\d+/');
    }

    #[Test]
    public function shouldBeEqualIgnoringCase()
    {
        Assert::thatString("Frodo")->isEqualToIgnoringCase('frodo');
    }

    #[Test]
    public function shouldNotBeEqualIgnoringCase()
    {
        CatchException::when(Assert::thatString("Frodo12"))->isEqualToIgnoringCase('frodo');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldContainSubstring()
    {
        Assert::thatString("Frodo")->contains('od');
    }

    #[Test]
    public function shouldFailIfDoesNotContainSubstring()
    {
        CatchException::when(Assert::thatString("Frodo"))->contains('invalid');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldStartWithPrefix()
    {
        Assert::thatString("Frodo")->startsWith('Fr');
    }

    #[Test]
    public function shouldFailIfDoesNotStartWithPrefix()
    {
        CatchException::when(Assert::thatString("Frodo"))->startsWith('invalid');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldEndWithPrefix()
    {
        Assert::thatString("Frodo")->endsWith('do');
    }

    #[Test]
    public function shouldFailIfDoesNotEndWithPrefix()
    {
        CatchException::when(Assert::thatString("Frodo"))->endsWith('invalid');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldAcceptCorrectSize()
    {
        Assert::thatString("Frodo")->hasSize(5);
    }

    #[Test]
    public function shouldFailIfDifferentSize()
    {
        CatchException::when(Assert::thatString("Frodo"))->hasSize(7);

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldAssertThatDoesNotContainSubstring()
    {
        Assert::thatString("Frodo")->doesNotContain('asd');
    }

    #[Test]
    public function shouldFailForAssertThatDoesNotContainSubstring()
    {
        CatchException::when(Assert::thatString("Frodo"))->doesNotContain('od');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldAssertThatStringsAreEqual()
    {
        Assert::thatString("Frodo")->isEqualTo('Frodo');
    }

    #[Test]
    public function shouldFailIfStringsAreNotEqual()
    {
        CatchException::when(Assert::thatString("Frodo"))->isEqualTo('od');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldAssertThatStringsAreNotEqual()
    {
        Assert::thatString("Frodo")->isNotEqualTo('asd');
    }

    #[Test]
    public function shouldFailIfStringsAreEqualWhenTheyShouldNotBe()
    {
        CatchException::when(Assert::thatString("Frodo"))->isNotEqualTo('Frodo');

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldCheckIsStringIsNull()
    {
        Assert::thatString(null)->isNull();
    }

    #[Test]
    public function shouldCheckIsStringIsNotNull()
    {
        Assert::thatString('Floki')->isNotNull();
    }

    #[Test]
    public function shouldCheckIsStringIsEmpty()
    {
        Assert::thatString('')->isEmpty();
    }

    #[Test]
    public function shouldCheckIsStringIsNotEmpty()
    {
        Assert::thatString('Lady Stoneheart')->isNotEmpty();
    }
}
