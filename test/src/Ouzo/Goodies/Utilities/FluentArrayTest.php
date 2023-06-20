<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FluentArrayTest extends TestCase
{
    #[Test]
    public function shouldSupportChaining()
    {
        //given
        $array = [
            1 => 2,
            2 => 3,
            3 => 3
        ];

        //when
        $transformed = FluentArray::from($array)
            ->values()
            ->filter(fn($item) => $item > 2)
            ->unique()
            ->values()
            ->toArray();

        //then
        $this->assertEquals([3], $transformed);
    }

    #[Test]
    public function shouldReturnArrayKeys()
    {
        //given
        $array = [1 => 'a', 2 => 'b', 3 => 'c'];

        //when
        $transformed = FluentArray::from($array)->keys()->toArray();

        //then
        $this->assertEquals([1, 2, 3], $transformed);
    }

    #[Test]
    public function shouldFlipArray()
    {
        //given
        $array = [1 => 'a', 2 => 'b', 3 => 'c'];

        //when
        $transformed = FluentArray::from($array)->flip()->toArray();

        //then
        $this->assertEquals(['a' => 1, 'b' => 2, 'c' => 3], $transformed);
    }

    #[Test]
    public function shouldFlattenArray()
    {
        //given
        $array = [[1, 2], [3, 4]];

        //when
        $flattened = FluentArray::from($array)->flatten()->toArray();

        //then
        $this->assertEquals([1, 2, 3, 4], $flattened);
    }

    #[Test]
    public function shouldConvertToMap()
    {
        //given
        $obj[0] = new stdClass();
        $obj[0]->field1 = 'key1';
        $obj[0]->field2 = 'value1';
        $obj[1] = new stdClass();
        $obj[1]->field1 = 'key2';
        $obj[1]->field2 = 'value2';

        //when
        $toMap = FluentArray::from($obj)
            ->toMap(Functions::extractField('field1'), Functions::extractField('field2'))
            ->toArray();

        //then
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2'], $toMap);
    }

    #[Test]
    public function shouldConvertToJson()
    {
        //given
        $array = [1 => 'a', 2 => 'b', 3 => 'c'];

        //when
        $json = FluentArray::from($array)->toJson();

        //then
        $this->assertEquals('{"1":"a","2":"b","3":"c"}', $json);
    }

    #[Test]
    public function shouldReturnArraysIntersection()
    {
        //given
        $a1 = ['1', '4', '5'];
        $a2 = ['1', '4', '6'];

        //when
        $intersection = FluentArray::from($a1)->intersect($a2)->toArray();

        //then
        $this->assertEquals(['1', '4'], $intersection);
    }

    #[Test]
    public function shouldReverseArray()
    {
        //given
        $array = ['1', '2'];

        //when
        $reversed = FluentArray::from($array)->reverse()->toArray();

        //then
        $this->assertEquals(['2', '1'], $reversed);
    }

    #[Test]
    public function shouldReturnTheFirstElement()
    {
        //given
        $array = ['1', '2'];

        //when
        $first = FluentArray::from($array)->firstOr(null);

        //then
        $this->assertEquals('1', $first);
    }

    #[Test]
    public function shouldReturnDefaultIfNoFirstElement()
    {
        //given
        $array = [];

        //when
        $first = FluentArray::from($array)->firstOr('default');

        //then
        $this->assertEquals('default', $first);
    }

    #[Test]
    public function shouldSkipElements()
    {
        //given
        $array = [1, 2, 3, 4, 5];

        //when
        $result = FluentArray::from($array)->skip(2)->toArray();

        //then
        $this->assertEquals([3, 4, 5], $result);
    }

    #[Test]
    public function shouldLimitElements()
    {
        //given
        $array = [1, 2, 3, 4, 5];

        //when
        $result = FluentArray::from($array)->limit(3)->toArray();

        //then
        $this->assertEquals([1, 2, 3], $result);
    }

    #[Test]
    public function shouldReturnObjectsUniqueByField()
    {
        //given
        $array = [
            new Product(['name' => 'bob']),
            new Product(['name' => 'bob']),
            new Product(['name' => 'john'])
        ];

        //when
        $uniqueByName = FluentArray::from($array)
            ->uniqueBy('name')
            ->toArray();

        //then
        Assert::thatArray($uniqueByName)
            ->onProperty('name')
            ->containsExactly('bob', 'john');
    }

    #[Test]
    public function shouldReturnObjectsUniqueByFunctionResults()
    {
        //given
        $array = [
            new Product(['name' => 'bob']),
            new Product(['name' => 'bob']),
            new Product(['name' => 'john'])
        ];

        //when
        $uniqueByName = FluentArray::from($array)
            ->uniqueBy(Functions::extract()->name)
            ->toArray();

        //then
        Assert::thatArray($uniqueByName)
            ->onProperty('name')
            ->containsExactly('bob', 'john');
    }

    #[Test]
    public function shouldReturnObjectsGroupedByFunctionResults()
    {
        //given
        $array = [
            $product1 = new Product(['name' => 'bob']),
            $product2 = new Product(['name' => 'john']),
            $product3 = new Product(['name' => 'bob'])
        ];

        //when
        $groupedByName = FluentArray::from($array)->groupBy(Functions::extract()->name)->toArray();

        //then
        $this->assertSame([
            'bob' => [$product1, $product3],
            'john' => [$product2]
        ], $groupedByName);
    }

    #[Test]
    public function shouldReturnObjectsUniqueByNestedField()
    {
        //given
        $category = new Category(['name' => 'cat1']);

        $product1 = new Product(['name' => 'bob']);
        $product1->category = $category;

        $product2 = new Product(['name' => 'john']);
        $product2->category = $category;

        $array = [$product1, $product2];

        //when
        $uniqueByName = FluentArray::from($array)
            ->uniqueBy('category->name')
            ->toArray();

        //then
        Assert::thatArray($uniqueByName)->hasSize(1);
    }

    #[Test]
    public function shouldSortElements()
    {
        //given
        $array = [4, 1, 3, 2];

        //when
        $result = FluentArray::from($array)->sort(Comparator::natural())->toArray();

        //then
        $this->assertEquals([1, 2, 3, 4], $result);
    }

    #[Test]
    public function shouldExecuteForEach()
    {
        //given
        $array = [1 => 'a', 2 => 'b', 3 => 'c'];
        $newArray = [];

        //when
        FluentArray::from($array)->each(function ($element) use (&$newArray) {
            $newArray[] = $element;
        });

        //then
        $this->assertEquals(['a', 'b', 'c'], $newArray);
    }

    #[Test]
    public function shouldGetDuplicates()
    {
        //given
        $array = ['a', 'b', 'c', 'b', 'c', 'b'];

        //when
        $result = FluentArray::from($array)->getDuplicates()->toArray();

        //then
        $this->assertEquals(['b', 'c'], $result);
    }

    #[Test]
    public function shouldGetDuplicatesAssoc()
    {
        //given
        $array = ['a', 'b', 'd', 'c', 'b', 'c', 'b'];

        //when
        $result = FluentArray::from($array)->getDuplicatesAssoc()->toArray();

        //then
        $this->assertEquals([1 => 'b', 3 => 'c'], $result);
    }

    #[DataProvider('associativeAndSequentialValueArrays')]
    public function shouldValuesResetInternalArrayPointer(array $inputArray): void
    {
        // given
        next($inputArray);

        // when
        $copiedArray = FluentArray::from($inputArray)->values()->toArray();

        // then
        $this->assertEquals('two', current($inputArray));
        $this->assertEquals('one', current($copiedArray));
    }

    public static function associativeAndSequentialValueArrays(): array
    {
        return [
            'associative' => [[
                'one' => 'one',
                'two' => 'two',
                'three' => 'three'
            ]],
            'sequential' => [[
                'one',
                'two',
                'three'
            ]],
        ];
    }
}
