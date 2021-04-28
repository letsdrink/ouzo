<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Comparator;
use Ouzo\Utilities\Functions;
use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFindIntZeroInArray()
    {
        //given
        $array = [
            'k1' => 4,
            'k2' => 'd',
            'k3' => 0,
            'k4' => '',
            'k5' => false,
            9 => 'p'
        ];
        //when
        $zeroKey = Arrays::findKeyByValue($array, 0);

        //then
        $this->assertEquals('k3', $zeroKey);
    }

    /**
     * @test
     */
    public function shouldMapKeys()
    {
        //given
        $array = [
            'k1' => 'v1',
            'k2' => 'v2',
            'k3' => 'v3',
        ];
        //when
        $arrayWithNewKeys = Arrays::mapKeys($array, fn($key) => "new_{$key}");

        //then
        $this->assertEquals([
            'new_k1' => 'v1',
            'new_k2' => 'v2',
            'new_k3' => 'v3',
        ], $arrayWithNewKeys);
    }

    /**
     * @test
     */
    public function shouldMapValues()
    {
        //given
        $array = ['k1', 'k2', 'k3'];

        //when
        $result = Arrays::map($array, fn($value) => "new_{$value}");

        //then
        $this->assertEquals(['new_k1', 'new_k2', 'new_k3'], $result);
    }

    /**
     * @test
     */
    public function shouldMapEntries()
    {
        //given
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        //when
        $result = Arrays::mapEntries($array, fn($key, $value): string => "{$key}_{$value}");

        //then
        $this->assertEquals(['a' => 'a_1', 'b' => 'b_2', 'c' => 'c_3'], $result);
    }

    /**
     * @test
     */
    public function shouldFilterValues()
    {
        //given
        $array = [1, 2, 3, 4];

        //when
        $result = Arrays::filter($array, fn($value) => $value > 2);

        //then
        $this->assertEquals([2 => 3, 3 => 4], $result);
    }

    /**
     * @test
     */
    public function shouldUseIdentityAsDefaultValueFunctionInToMap()
    {
        //given
        $array = range(1, 2);

        //when
        $map = Arrays::toMap($array, fn($elem) => $elem * 10);

        //then
        $this->assertEquals([10 => 1, 20 => 2], $map);
    }

    /**
     * @test
     */
    public function shouldGetLastElementOfArray()
    {
        //given
        $array = ['a', 'b', 'c'];

        //when
        $last = Arrays::last($array);

        //then
        $this->assertEquals('c', $last);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenElementsAreEmptyInLast()
    {
        $this->expectException(InvalidArgumentException::class);

        Arrays::last([]);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenElementsAreEmptyInFirst()
    {
        $this->expectException(InvalidArgumentException::class);

        Arrays::first([]);
    }

    /**
     * @test
     */
    public function shouldGetFirstKeyInteger()
    {
        //given
        $array = [3 => 'bar', 4 => 'example'];

        //when
        $first = Arrays::first($array);

        // then
        Assert::thatString($first)->isEqualTo('bar');
    }

    /**
     * @test
     */
    public function shouldGetFirstKeyString()
    {
        //given
        $array = ['foo' => 'bar', 0 => 'foo', 2 => 'example'];

        //when
        $first = Arrays::first($array);

        // then
        Assert::thatString($first)->isEqualTo('bar');
    }

    /**
     * @test
     */
    public function shouldGetFirstKeyAccordingToOrder()
    {
        //given
        $array = [1 => 'bar', 0 => 'foo'];

        //when
        $first = Arrays::first($array);

        // then
        Assert::thatString($first)->isEqualTo('bar');
    }

    /**
     * @test
     */
    public function shouldReturnNullIfNotFoundFirstElement()
    {
        //given
        $array = [];

        //when
        $return = Arrays::firstOrNull($array);

        //then
        $this->assertNull($return);
    }

    /**
     * @test
     */
    public function shouldReturnTrueIfAllElementSatisfyPredicate()
    {
        //given
        $array = [1, 2];

        //when
        $all = Arrays::all($array, fn($element) => $element < 3);

        //then
        $this->assertTrue($all);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfNotAllElementSatisfyPredicate()
    {
        //given
        $array = [1, 2, 3];

        //when
        $all = Arrays::all($array, fn($element) => $element < 3);

        //then
        $this->assertFalse($all);
    }

    /**
     * @test
     */
    public function shouldCheckIsAnyIsBool()
    {
        //given
        $array = ['a', true, 'c'];

        //when
        $any = Arrays::any($array, fn($element) => is_bool($element));

        //then
        $this->assertTrue($any);
    }

    /**
     * @test
     */
    public function shouldFilterByAllowedKeys()
    {
        //given
        $array = ['a' => 1, 'b' => 2, 'c' => 3];

        //when
        $filtered = Arrays::filterByAllowedKeys($array, ['a', 'b']);

        //then
        $this->assertEquals(['a' => 1, 'b' => 2], $filtered);
    }

    /**
     * @test
     */
    public function shouldFilterByKeys()
    {
        //given
        $array = ['a1' => 1, 'a2' => 2, 'c' => 3];

        //when
        $filtered = Arrays::filterByKeys($array, fn($elem) => $elem[0] == 'a');

        //then
        $this->assertEquals(['a1' => 1, 'a2' => 2], $filtered);
    }

    /**
     * @test
     */
    public function shouldGroupByFunctionResult()
    {
        //given
        $product1 = new Product(['name' => 'a', 'description' => '1']);
        $product2 = new Product(['name' => 'b', 'description' => '2']);
        $product3 = new Product(['name' => 'c', 'description' => '2']);
        $array = [$product1, $product2, $product3];

        //when
        $grouped = Arrays::groupBy($array, Functions::extractField('description'));

        //then
        $this->assertEquals([
            '1' => [$product1],
            '2' => [$product2, $product3]], $grouped);
    }

    /**
     * @test
     */
    public function shouldHandleEmptyArrayInGroupBy()
    {
        //given
        $array = [];

        //when
        $grouped = Arrays::groupBy($array, Functions::extractField('field'));

        //then
        $this->assertEmpty($grouped);
    }

    /**
     * @test
     */
    public function shouldGroupByAndSort()
    {
        //given
        $product1 = new Product(['name' => 'a', 'description' => '1', 'id_category' => '1']);
        $product2 = new Product(['name' => 'b', 'description' => '2', 'id_category' => '2']);
        $product3 = new Product(['name' => 'c', 'description' => '2', 'id_category' => '1']);
        $array = [$product1, $product2, $product3];

        //when
        $grouped = Arrays::groupBy($array, Functions::extractField('description'), 'id_category');

        //then
        $this->assertEquals([
            '1' => [$product1],
            '2' => [$product3, $product2]], $grouped);
    }

    /**
     * @test
     */
    public function shouldSortArrayByField()
    {
        //given
        $product1 = new Product(['id_category' => '2']);
        $product2 = new Product(['id_category' => '3']);
        $product3 = new Product(['id_category' => '1']);
        $array = [$product1, $product2, $product3];

        //when
        $sorted = Arrays::orderBy($array, 'id_category');

        //then
        $this->assertEquals([$product3, $product1, $product2], $sorted);
    }

    /**
     * @test
     */
    public function shouldSortArrayByCompoundComparator()
    {
        //given
        $product1 = new Product(['name' => 'b', 'description' => '2']);
        $product2 = new Product(['name' => 'a', 'description' => '1']);
        $product3 = new Product(['name' => 'a', 'description' => '2']);

        $array = [$product3, $product1, $product2];

        $comparator = Comparator::compound(Comparator::reverse(Comparator::compareBy('name')), Comparator::compareBy('description'));

        //when
        $sorted = Arrays::sort($array, $comparator);

        //then
        Assert::thatArray($sorted)->containsExactly($product1, $product2, $product3);
    }

    /**
     * @test
     */
    public function shouldSortArrayByCompareByWithMultipleExpressions()
    {
        //given
        $product1 = new Product(['name' => 'a', 'description' => '2']);
        $product2 = new Product(['name' => 'b', 'description' => '2']);
        $product3 = new Product(['name' => 'a', 'description' => '1']);

        $array = [$product1, $product2, $product3];

        $comparator = Comparator::compareBy('name', 'description');

        //when
        $sorted = Arrays::sort($array, $comparator);

        //then
        Assert::thatArray($sorted)->containsExactly($product3, $product1, $product2);
    }

    /**
     * @test
     */
    public function shouldSortArrayIndependentlyFromInitialOrder()
    {
        //given
        $product1 = new Product(['name' => 'a']);
        $product2 = new Product(['name' => 'b']);
        $product3 = new Product(['name' => 'c']);

        $array1 = [$product1, $product2, $product3];
        $array2 = [$product2, $product1, $product3];
        $array3 = [$product3, $product1, $product2];

        $comparator = Comparator::compareBy('name');

        //when
        $sorted1 = Arrays::sort($array1, $comparator);
        $sorted2 = Arrays::sort($array2, $comparator);
        $sorted3 = Arrays::sort($array3, $comparator);

        //then
        $this->assertEquals($sorted1, $sorted2);
        $this->assertEquals($sorted2, $sorted3);
    }

    /**
     * @test
     */
    public function shouldSortArrayByDefaultExtractor()
    {
        //given
        $array = [1, 3, 2];

        //when
        $sorted = Arrays::sort($array, Comparator::natural());

        //then
        $this->assertEquals([1, 2, 3], $sorted);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnSameArrayForArray()
    {
        // given
        $array = [1, 2, 3];

        // when
        $result = Arrays::toArray($array);

        // then
        $this->assertEquals($array, $result);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnArrayForNonArray()
    {
        // when
        $result = Arrays::toArray('test');

        // then
        $this->assertEquals(['test'], $result);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnEmptyArrayForNull()
    {
        // when
        $result = Arrays::toArray(null);

        // then
        $this->assertEquals([], $result);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnArrayWithOneElementForFalse()
    {
        // when
        $result = Arrays::toArray(false);

        // then
        $this->assertEquals([false], $result);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnArrayWithOneElementForZero()
    {
        // when
        $result = Arrays::toArray(0);

        // then
        $this->assertEquals([0], $result);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnArrayWithOneElementForEmptyString()
    {
        // when
        $result = Arrays::toArray('');

        // then
        $this->assertEquals([''], $result);
    }

    /**
     * @test
     */
    public function shouldGetRandomElement()
    {
        //given
        $array = [1, 3, 6, 9];

        //when
        $result = Arrays::randElement($array);

        //then
        $this->assertContains($result, $array);
    }

    /**
     * @test
     */
    public function shouldReturnNullIfNotFindRandomElement()
    {
        //given
        $array = [];

        //when
        $result = Arrays::randElement($array);

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldGetValueFromArray()
    {
        //given
        $array = ['id' => 1, 'name' => 'john'];

        //when
        $value = Arrays::getValue($array, 'name');

        //then
        $this->assertEquals('john', $value);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultValueIfNotGetValueFromArray()
    {
        //given
        $array = ['id' => 1, 'name' => 'john'];

        //when
        $value = Arrays::getValue($array, 'surname', '--not found--');

        //then
        $this->assertEquals('--not found--', $value);
    }

    /**
     * @test
     */
    public function shouldReturnCombinedArray()
    {
        //given
        $keys = ['id', 'name', 'surname'];
        $values = [1, 'john', 'smith'];

        //when
        $combined = Arrays::combine($keys, $values);

        //then
        Assert::thatArray($combined)
            ->hasSize(3)
            ->containsKeyAndValue(['id' => 1, 'name' => 'john', 'surname' => 'smith']);
    }

    /**
     * @test
     */
    public function shouldFlattenAnArray()
    {
        //given
        $array = [
            'names' => [
                'john',
                'peter',
                'bill'
            ],
            'products' => [
                'cheese',
                'test' => [
                    'natural' => 'milk',
                    'brie'
                ]
            ]
        ];

        //when
        $flatten = Arrays::flatten($array);

        //then
        Assert::thatArray($flatten)
            ->hasSize(6)
            ->containsExactly('john', 'peter', 'bill', 'cheese', 'milk', 'brie');
    }

    /**
     * @test
     */
    public function shouldCheckIsKeyExists()
    {
        //given
        $array = ['id' => 1, 'name' => 'john'];

        //when
        $return = Arrays::keyExists($array, 'name');

        //then
        $this->assertTrue($return);
    }

    /**
     * @test
     */
    public function shouldReduceAnArray()
    {
        //given
        $array = ['$id', '$name', '$phone'];

        //when
        $reduced = Arrays::reduce($array, function ($result, $element) {
            if ($result == null) {
                $result .= 'isset(' . $element . ') && ';
            } else {
                $result .= ' && isset(' . $element . ')';
            }
            return rtrim($result, '&& ');
        });

        //then
        $this->assertEquals('isset($id) && isset($name) && isset($phone)', $reduced);
    }

    /**
     * @test
     */
    public function shouldFindElement()
    {
        //when
        $value = Arrays::find(['a', 'b', 'c'], function ($element) {
            return $element == 'b';
        });

        //then
        $this->assertEquals('b', $value);
    }

    /**
     * @test
     */
    public function findShouldReturnNullWhenElementWasNotFound()
    {
        //when
        $value = Arrays::find(['a', 'c'], function ($element) {
            return $element == 'b';
        });

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function shouldReturnArraysIntersection()
    {
        //given
        $a1 = ['1', '4', '5'];
        $a2 = ['1', '4', '6'];

        //when
        $intersection = Arrays::intersect($a1, $a2);

        //then
        $this->assertEquals(['1', '4'], $intersection);
    }

    /**
     * @test
     */
    public function shouldReturnNestedValue()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2', '3']);

        //then
        $this->assertEquals('value', $value);
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnNullWhenKeyNotFound()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::getNestedValue($array, ['1', '4']);

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnNullWhenZeroStringKeyNotFound()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2', '3', '0']);

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnNullWhenZeroIntKeyNotFound()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2', '3', 0]);

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnNullWhenMultipleStringZeroKeysNotFound()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2', '3', '0', '0', '0']);

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnEmptyValue()
    {
        //given
        $array = ['1' => ['2' => '']];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2']);

        //then
        $this->assertTrue($value === '');
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnZero()
    {
        //given
        $array = ['1' => ['2' => '0']];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2']);

        //then
        $this->assertTrue($value === '0');
    }

    /**
     * @test
     */
    public function shouldSetNestedValue()
    {
        //given
        $array = [];

        //when
        Arrays::setNestedValue($array, ['1', '2', '3'], 'value');

        //then
        $this->assertEquals(['1' => ['2' => ['3' => 'value']]], $array);
    }

    /**
     * @test
     */
    public function shouldRemoveNestedKeyAtRoot()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        Arrays::removeNestedKey($array, ['1']);

        //then
        $this->assertEquals([], $array);
    }

    /**
     * @test
     */
    public function shouldRemoveNestedKeyWithoutEmptyParent()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        Arrays::removeNestedKey($array, ['1', '2']);

        //then
        $this->assertEquals(['1' => []], $array);
    }

    /**
     * @test
     */
    public function shouldRemoveNestedKeyWithEmptyParent()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        Arrays::removeNestedKey($array, ['1', '2'], Arrays::REMOVE_EMPTY_PARENTS);

        //then
        $this->assertEquals([], $array);
    }

    /**
     * @test
     */
    public function shouldNotRemoveNestedKeyWhenKeyNotFoundAndValueIsNull()
    {
        //given
        $array = ['1' => null];

        //when
        Arrays::removeNestedKey($array, ['1', '2']);

        //then
        $this->assertEquals(['1' => null], $array);
    }

    /**
     * @test
     */
    public function shouldNotChangeInputArrayWhenKeyNotFound()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        Arrays::removeNestedKey($array, ['1', '2', '3', '0']);

        //then
        $this->assertEquals(['1' => ['2' => ['3' => 'value']]], $array);
    }

    /**
     * @test
     */
    public function shouldThrowDeprecatedExceptionWhenUseRemoveNestedValue()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        try {
            /** @noinspection PhpDeprecationInspection */
            Arrays::removeNestedValue($array, ['1', '2']);
        } catch (Exception $e) {//then
            $this->assertEquals('Use Arrays::removeNestedKey instead', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function shouldNotRemoveNestedKeyWhenKeyNotFound()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        Arrays::removeNestedKey($array, ['1', '4']);

        //then
        $this->assertEquals(['1' => ['2' => ['3' => 'value']]], $array);
    }

    /**
     * @test
     */
    public function shouldNotRemoveKeyWhenKeyNotFoundInTheFirstLevel()
    {
        //given
        $array = ['1' => ['2' => 'value']];

        //when
        Arrays::removeNestedKey($array, ['2', '4']);

        //then
        $this->assertEquals(['1' => ['2' => 'value']], $array);
    }

    /**
     * @test
     */
    public function shouldCheckIfArrayHasNestedKey()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::hasNestedKey($array, ['1', '2', '3']);

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function shouldCheckIfArrayHasNestedKeyForRoot()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::hasNestedKey($array, ['1']);

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnFalseWhenKeyDoesNotExist()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::hasNestedKey($array, ['1', '4']);

        //then
        $this->assertFalse($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnTrueWhenKeyIsNullAndNullIsValue()
    {
        //given
        $array = ['1' => ['2' => ['3' => null]]];

        //when
        $value = Arrays::hasNestedKey($array, ['1', '2', '3'], Arrays::TREAT_NULL_AS_VALUE);

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnFalseWhenKeyIsNullAndNullIsNotValue()
    {
        //given
        $array = ['1' => ['2' => ['3' => null]]];

        //when
        $value = Arrays::hasNestedKey($array, ['1', '2', '3']);

        //then
        $this->assertFalse($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnFalseWhenKeyStringZeroDoesNotExist()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::hasNestedKey($array, ['1', '2', '3', '0']);

        //then
        $this->assertFalse($value);
    }

    /**
     * @test
     */
    public function shouldFilterNotBlank()
    {
        //given
        $array = [
            0 => 'foo',
            1 => false,
            2 => -1,
            3 => null,
            4 => ''
        ];

        //when
        $filtered = Arrays::filterNotBlank($array);

        //then
        Assert::thatArray($filtered)->hasSize(2)->contains('foo', -1);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayWhenNotFoundInNestedValue()
    {
        //given
        $array = ['1' => ['2' => ['3' => 'value']]];

        //when
        $value = Arrays::getNestedValue($array, ['1', '2', '3', '4']);

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function shouldCreateArrayWithFlattenKeys()
    {
        //given
        $array = [
            'customer' => [
                'name' => 'Name',
                'phone' => '123456789',
            ],
            'other' => [
                'ids_map' => [
                    '1qaz' => 'qaz',
                    '2wsx' => 'wsx'
                ],
                'first' => [
                    'second' => [
                        'third' => 'some value'
                    ]
                ]
            ]
        ];

        //when
        $flatten = Arrays::flattenKeysRecursively($array);

        //then
        $expected = [
            'customer.name' => 'Name',
            'customer.phone' => '123456789',
            'other.ids_map.1qaz' => 'qaz',
            'other.ids_map.2wsx' => 'wsx',
            'other.first.second.third' => 'some value'
        ];
        $this->assertEquals($expected, $flatten);
    }

    /**
     * @test
     */
    public function shouldCountElements()
    {
        //given
        $array = [1, 2, 3];

        //when
        $count = Arrays::count($array, function ($element) {
            return $element < 3;
        });

        //then
        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function shouldReturnObjectsUniqueByField()
    {
        //given
        $array = [
            new Product(['name' => 'bob']),
            new Product(['name' => 'bob']),
            new Product(['name' => 'john'])
        ];

        //when
        $uniqueByName = Arrays::uniqueBy($array, 'name');

        //then
        Assert::thatArray($uniqueByName)->onProperty('name')->containsExactly('bob', 'john');
    }

    /**
     * @test
     */
    public function shouldReturnObjectsUniqueByFunctionResults()
    {
        //given
        $array = [
            new Product(['name' => 'bob']),
            new Product(['name' => 'bob']),
            new Product(['name' => 'john'])
        ];

        //when
        $uniqueByName = Arrays::uniqueBy($array, Functions::extract()->name);

        //then
        Assert::thatArray($uniqueByName)->onProperty('name')->containsExactly('bob', 'john');
    }

    /**
     * @test
     */
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
        $uniqueByName = Arrays::uniqueBy($array, 'category->name');

        //then
        Assert::thatArray($uniqueByName)->hasSize(1);
    }

    /**
     * @test
     */
    public function shouldExtractRecursivelyArrayColumn()
    {
        //given
        $array = [
            ['id' => 123, 'name' => 'value1', 'test' => ['number' => 90]],
            ['id' => 123, 'name' => 'value1', 'test' => ['number' => 100]]
        ];

        //when
        $numbers = Arrays::map($array, Functions::extract()->test->number);

        //then
        Assert::thatArray($numbers)->hasSize(2)
            ->containsOnly(90, 100);
    }

    /**
     * @test
     */
    public function shouldReturnAdditionalValueInRecursiveDiff()
    {
        //given
        $array1 = ['a' => ['b' => 'c', 'd' => 'e'], 'f' => ['b' => 'c'], 'z'];
        $array2 = ['a' => ['b' => 'c']];

        //when
        $recursiveDiff = Arrays::recursiveDiff($array1, $array2);

        //then
        $this->assertEquals(['a' => ['d' => 'e'], 'f' => ['b' => 'c'], 'z'], $recursiveDiff);
    }

    /**
     * @test
     */
    public function shouldCheckIfArrayIsAssociative()
    {
        $this->assertTrue(Arrays::isAssociative(['a' => 1, 2, 'c']));
        $this->assertFalse(Arrays::isAssociative([1 => 1, 2, 'c']));
        $this->assertFalse(Arrays::isAssociative(['a', 'b', 'c']));
    }

    /**
     * @test
     */
    public function shouldConcatArrays()
    {
        //given
        $arrays = [[1, 2], [3, 4]];

        //when
        $flattened = Arrays::concat($arrays);

        //then
        $this->assertEquals([1, 2, 3, 4], $flattened);
    }

    /**
     * @test
     */
    public function shouldConcatEmptyArray()
    {
        //given
        $array = [];

        //when
        $flattened = Arrays::concat($array);

        //then
        $this->assertEquals([], $flattened);
    }

    /**
     * @test
     */
    public function containsShouldWorkForDifferentTypes()
    {
        $this->assertTrue(Arrays::contains([1, 2, 3], 1));
    }

    /**
     * @test
     */
    public function shouldReturnShuffledArrayWithKeyAssociation()
    {
        //given
        $array = [1 => 'a', 2 => 'b', 3 => 'c'];

        //when
        $result = Arrays::shuffle($array);

        //then
        Assert::thatArray($result)
            ->containsKeyAndValue([1 => 'a'])
            ->containsKeyAndValue([2 => 'b'])
            ->containsKeyAndValue([3 => 'c']);
    }

    /**
     * @test
     */
    public function shuffleShouldReturnEmptyArrayForEmptyArray()
    {
        $this->assertEmpty(Arrays::shuffle([]));
    }

    /**
     * @test
     */
    public function shouldGetDuplicates()
    {
        $this->assertEquals(['a', 'b', 'd'], Arrays::getDuplicates(['1', 'a', 'b', 'c', 'd', 'b', 'b', 'd', 'b', 'd', 'a']));
        $this->assertEmpty(Arrays::getDuplicates(['a', 'b', 'c']));
        $this->assertEmpty(Arrays::getDuplicates([]));
    }

    /**
     * @test
     */
    public function shouldGetDuplicatesAssoc()
    {
        $this->assertEquals([2 => 'a', 3 => 'b', 6 => 'c'], Arrays::getDuplicatesAssoc(['1', '2', 'a', 'b', '3', 'b', 'c', 'b', 'c', 'b', 'c', 'a']));
        $this->assertEmpty(Arrays::getDuplicatesAssoc(['a', 'b', 'c']));
        $this->assertEmpty(Arrays::getDuplicatesAssoc([]));
    }

    /**
     * @test
     */
    public function shouldGetValues()
    {
        //given
        $array = ['id' => 4, 'name' => 'Arya', 'surname' => 'Stark'];

        //when
        $keys = Arrays::values($array);

        //then
        Assert::thatArray($keys)->isEqualTo([4, 'Arya', 'Stark']);
    }

    /**
     * @test
     */
    public function shouldResetInternalArrayPointer()
    {
        //given
        $array = ['one', 'two', 'three'];
        next($array);

        //when
        $values = Arrays::values($array);

        //then
        $this->assertEquals('one', current($values));
    }

    /**
     * @test
     */
    public function shouldGetKeys()
    {
        //given
        $array = ['id' => 4, 'name' => 'Arya', 'surname' => 'Stark'];

        //when
        $keys = Arrays::keys($array);

        //then
        Assert::thatArray($keys)->isEqualTo(['id', 'name', 'surname']);
    }
}
