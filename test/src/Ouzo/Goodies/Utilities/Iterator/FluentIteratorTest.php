<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Utilities\Iterator;

use Ouzo\Tests\CatchException;
use Ouzo\Utilities\FluentFunctions;

class FluentIteratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldMapItems()
    {
        //given
        $iterator = new \ArrayIterator(array(2, 3, 4, 5));

        //when
        $mappedIterator = FluentIterator::from($iterator)->map(function ($item) {
            return $item + 1;
        });

        //then
        $this->assertEquals(array(3, 4, 5, 6), $mappedIterator->toArray());
    }

    /**
     * @test
     */
    public function shouldSkipAndLimitItems()
    {
        //given
        $iterator = new \ArrayIterator(array(1, 2, 3, 4, 5));

        //when
        $result = FluentIterator::from($iterator)->skip(1)->limit(2);

        //then
        $this->assertEquals(array(2, 3), array_values($result->toArray()));
    }

    /**
     * @test
     */
    public function shouldBatchIteratorElements()
    {
        //given
        $iterator = new \ArrayIterator(array(1, 2, 3, 4));

        //when
        $result = FluentIterator::from($iterator)->batch(2);

        //then
        $this->assertEquals(array(array(1, 2), array(3, 4)), $result->toArray());
    }

    /**
     * @test
     */
    public function shouldCycleIndefinitely()
    {
        //given
        $iterator = new \ArrayIterator(array(1, 2, 3, 4));

        //when
        $result = FluentIterator::from($iterator)->cycle()->limit(10)->reindex();

        //then
        $this->assertEquals(array(1, 2, 3, 4, 1, 2, 3, 4, 1, 2), $result->toArray());
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
        $this->assertEquals(array(1, 1, 1), $result->toArray());
    }

    /**
     * @test
     */
    public function shouldFilterIteratorElements()
    {
        //given
        $iterator = new \ArrayIterator(array('a', 'pref_a', 'pref_b', 'b'));

        //when
        $result = FluentIterator::from($iterator)->filter(FluentFunctions::startsWith('pref'))->reindex();

        //then
        $this->assertEquals(array('pref_a', 'pref_b'), $result->toArray());
    }

    /**
     * @test
     */
    public function shouldReturnFirstElementOrDefault()
    {
        $this->assertEquals('a', FluentIterator::fromArray(array('a'))->firstOr('default'));
        $this->assertEquals('default', FluentIterator::fromArray(array())->firstOr('default'));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfFirstCalledForEmptyIterator()
    {
        //given
        $iterator = FluentIterator::fromArray(array());

        // when
        CatchException::when($iterator)->first();

        // then
        CatchException::assertThat()->isInstanceOf('\InvalidArgumentException');
    }

    /**
     * @test
     */
    public function shouldReturnFirstElementInIterator()
    {
        //given
        $iterator = FluentIterator::fromArray(array('a', 'b'));

        // when
        $first = $iterator->first();

        // then
        $this->assertEquals('a', $first);
    }
}
