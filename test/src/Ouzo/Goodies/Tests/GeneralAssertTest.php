<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\CatchException;
use Ouzo\Tests\GeneralAssert;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class GeneralAssertTest extends TestCase
{
    #[Test]
    public function shouldReturnInstance()
    {
        // when
        $instance = GeneralAssert::that(0);

        // then
        $this->assertInstanceOf(GeneralAssert::class, $instance);
    }

    #[Test]
    public function shouldBeInstanceOf()
    {
        // then
        GeneralAssert::that(new stdClass())->isInstanceOf(stdClass::class);
        GeneralAssert::that(Mock::create(stdClass::class))->isInstanceOf(stdClass::class);
    }

    #[DataProvider('notInstanceOf')]
    public function shouldNotBeInstanceOf(mixed $instance, string $name): void
    {
        CatchException::when(GeneralAssert::that($instance))->isInstanceOf($name);

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    public static function notInstanceOf(): array
    {
        return [
            [[], stdClass::class],
            [4, stdClass::class],
            [true, stdClass::class],
            [new Example(), stdClass::class],
            [new stdClass(), Example::class]
        ];
    }

    #[Test]
    public function shouldBeNull()
    {
        GeneralAssert::that(null)->isNull();
    }

    #[DataProvider('notNull')]
    public function shouldBeNotNull(mixed $notNull): void
    {
        GeneralAssert::that($notNull)->isNotNull();
    }

    public static function notEqualToNull(): array
    {
        return [
            [1],
            ['1'],
            ['0'],
            [5.4],
            ['word'],
            [true],
            ['true'],
            ['false'],
            [[]]
        ];
    }

    #[DataProvider('notNull')]
    public function shouldNotBeNull(mixed $notNull): void
    {
        CatchException::when(GeneralAssert::that($notNull))->isNull();

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[Test]
    public function shouldNotBeNotNull()
    {
        CatchException::when(GeneralAssert::that(null))->isNotNull();

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    #[DataProvider('notNull')]
    public function shouldBeEqual(mixed $notNull): void
    {
        GeneralAssert::that($notNull)->isEqualTo($notNull);
    }

    #[DataProvider('notEqualToNull')]
    public function shouldNotBeEqual(mixed $notNull): void
    {
        CatchException::when(GeneralAssert::that(null))->isEqualTo($notNull);

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    public static function notNull(): array
    {
        return [
            [1],
            [0],
            ['1'],
            [''],
            ['0'],
            [5.4],
            ['word'],
            [true],
            ['true'],
            ['false'],
            [[]]
        ];
    }
}

class Example
{
}
