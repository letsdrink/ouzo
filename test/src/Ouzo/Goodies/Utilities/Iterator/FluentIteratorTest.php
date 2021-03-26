<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Utilities\Iterator;

use InvalidArgumentException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\MethodCall;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentFunctions;

use PHPUnit\Framework\TestCase; 

class FluentIteratorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldMapItems()
    {
        //given
        $iterator = new \ArrayIterator([2, 3, 4, 5]);

        //when
        $mappedIterator = FluentIterator::from($iterator)->map(function ($item) {
            return $item + 1;
        });

        //then
        $this->assertEquals([3, 4, 5, 6], $mappedIterator->toArray());
    }

    /**
     * @test
     */
    public function shouldSkipAndLimitItems()
    {
        //given
        $iterator = new \ArrayIterator([1, 2, 3, 4, 5]);

        //when
        $result = FluentIterator::from($iterator)->skip(1)->limit(2);

        //then
        $this->assertEquals([2, 3], array_values($result->toArray()));
    }

    /**
     * @test
     */
    public function shouldBatchIteratorElements()
    {
        //given
        $iterator = new \ArrayIterator([1, 2, 3, 4]);

        //when
        $result = FluentIterator::from($iterator)->batch(2);

        //then
        $this->assertEquals([[1, 2], [3, 4]], $result->toArray());
    }

    /**
     * @test
     */
    public function shouldCycleIndefinitely()
    {
        //given
        $iterator = new \ArrayIterator([1, 2, 3, 4]);

        //when
        $result = FluentIterator::from($iterator)->cycle()->limit(10)->reindex();

        //then
        $this->assertEquals([1, 2, 3, 4, 1, 2, 3, 4, 1, 2], $result->toArray());
    }

    /**
     * @test
     */
    public function shouldGenerateValues()
    {
        //when
        $generator = function () {
            return 1;
        };

        $result = FluentIterator::fromFunction($generator)->limit(3);

        //then
        $this->assertEquals([1, 1, 1], $result->toArray());
    }

    /**
     * @test
     */
    public function shouldFilterIteratorElements()
    {
        //given
        $iterator = new \ArrayIterator(['a', 'pref_a', 'pref_b', 'b']);

        //when
        $result = FluentIterator::from($iterator)->filter(FluentFunctions::startsWith('pref'))->reindex();

        //then
        $this->assertEquals(['pref_a', 'pref_b'], $result->toArray());
    }

    /**
     * @test
     */
    public function shouldReturnFirstElementOrDefault()
    {
        $this->assertEquals('a', FluentIterator::fromArray(['a'])->firstOr('default'));
        $this->assertEquals('default', FluentIterator::fromArray([])->firstOr('default'));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfFirstCalledForEmptyIterator()
    {
        //given
        $iterator = FluentIterator::fromArray([]);

        // when
        CatchException::when($iterator)->first();

        // then
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function shouldReturnFirstElementInIterator()
    {
        //given
        $iterator = FluentIterator::fromArray(['a', 'b']);

        // when
        $first = $iterator->first();

        // then
        $this->assertEquals('a', $first);
    }

    /**
     * @test
     */
    public function shouldNotCallMapFunctionOnSkippedElements()
    {
        //given
        $iterator = new \ArrayIterator([1, 2, 3]);
        $mapper = Mock::create();
        Mock::when($mapper)->map(Mock::anyArgList())->thenAnswer(function (MethodCall $methodCall) {
            return Arrays::first($methodCall->arguments);
        });

        //when
        $result = FluentIterator::from($iterator)
            ->map(function ($elem) use ($mapper) {
                return $mapper->map($elem);
            })
            ->skip(1)
            ->limit(1);

        //then
        $this->assertEquals([2], array_values($result->toArray()));
        Mock::verify($mapper)->neverReceived()->map(1);
        Mock::verify($mapper)->neverReceived()->map(3);
    }
}
