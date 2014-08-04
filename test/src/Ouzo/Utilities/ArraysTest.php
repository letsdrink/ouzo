<?php
use Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class ArraysTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFindIntZeroInArray()
    {
        //given
        $array = array(
            'k1' => 4,
            'k2' => 'd',
            'k3' => 0,
            9 => 'p'
        );
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
        $array = array(
            'k1' => 'v1',
            'k2' => 'v2',
            'k3' => 'v3',
        );
        //when
        $arrayWithNewKeys = Arrays::mapKeys($array, function ($key) {
            return 'new_' . $key;
        });

        //then
        $this->assertEquals(array(
            'new_k1' => 'v1',
            'new_k2' => 'v2',
            'new_k3' => 'v3',
        ), $arrayWithNewKeys);
    }

    /**
     * @test
     */
    public function shouldMapValues()
    {
        //given
        $array = array('k1', 'k2', 'k3');

        //when
        $result = Arrays::map($array, function ($value) {
            return 'new_' . $value;
        });

        //then
        $this->assertEquals(array('new_k1', 'new_k2', 'new_k3'), $result);
    }

    /**
     * @test
     */
    public function shouldFilterValues()
    {
        //given
        $array = array(1, 2, 3, 4);

        //when
        $result = Arrays::filter($array, function ($value) {
            return $value > 2;
        });

        //then
        $this->assertEquals(array(2 => 3, 3 => 4), $result);
    }

    /**
     * @test
     */
    public function shouldUseIdentityAsDefaultValueFunctionInToMap()
    {
        //given
        $array = range(1, 2);

        //when
        $map = Arrays::toMap($array, function ($elem) {
            return $elem * 10;
        });

        //then
        $this->assertEquals(array(10 => 1, 20 => 2), $map);
    }

    /**
     * @test
     */
    public function shouldGetLastElementOfArray()
    {
        //given
        $array = array('a', 'b', 'c');

        //when
        $last = Arrays::last($array);

        //then
        $this->assertEquals('c', $last);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenElementsAreEmptyInLast()
    {
        //given
        $array = array();

        //when
        Arrays::last($array);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenElementsAreEmptyInFirst()
    {
        //given
        $array = array();

        //when
        Arrays::first($array);
    }

    /**
     * @test
     */
    public function shouldReturnNullIfNotFoundFirstElement()
    {
        //given
        $array = array();

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
        $array = array(1, 2);

        //when
        $all = Arrays::all($array, function ($element) {
            return $element < 3;
        });

        //then
        $this->assertTrue($all);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfNotAllElementSatisfyPredicate()
    {
        //given
        $array = array(1, 2, 3);

        //when
        $all = Arrays::all($array, function ($element) {
            return $element < 3;
        });

        //then
        $this->assertFalse($all);
    }

    /**
     * @test
     */
    public function shouldCheckIsAnyIsBool()
    {
        //given
        $array = array('a', true, 'c');

        //when
        $any = Arrays::any($array, function ($element) {
            return is_bool($element);
        });

        //then
        $this->assertTrue($any);
    }

    /**
     * @test
     */
    public function shouldFilterByAllowedKeys()
    {
        //given
        $array = array('a' => 1, 'b' => 2, 'c' => 3);

        //when
        $filtered = Arrays::filterByAllowedKeys($array, array('a', 'b'));

        //then
        $this->assertEquals(array('a' => 1, 'b' => 2), $filtered);
    }

    /**
     * @test
     */
    public function shouldFilterByKeys()
    {
        //given
        $array = array('a1' => 1, 'a2' => 2, 'c' => 3);

        //when
        $filtered = Arrays::filterByKeys($array, function ($elem) {
            return $elem[0] == 'a';
        });

        //then
        $this->assertEquals(array('a1' => 1, 'a2' => 2), $filtered);
    }

    /**
     * @test
     */
    public function shouldGroupByFunctionResult()
    {
        //given
        $product1 = new Product(array('name' => 'a', 'description' => '1'));
        $product2 = new Product(array('name' => 'b', 'description' => '2'));
        $product3 = new Product(array('name' => 'c', 'description' => '2'));
        $array = array($product1, $product2, $product3);

        //when
        $grouped = Arrays::groupBy($array, Functions::extractField('description'));

        //then
        $this->assertEquals(array(
                '1' => array($product1),
                '2' => array($product2, $product3))
            , $grouped);
    }

    /**
     * @test
     */
    public function shouldHandleEmptyArrayInGroupBy()
    {
        //given
        $array = array();

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
        $product1 = new Product(array('name' => 'a', 'description' => '1', 'id_category' => '1'));
        $product2 = new Product(array('name' => 'b', 'description' => '2', 'id_category' => '2'));
        $product3 = new Product(array('name' => 'c', 'description' => '2', 'id_category' => '1'));
        $array = array($product1, $product2, $product3);

        //when
        $grouped = Arrays::groupBy($array, Functions::extractField('description'), 'id_category');

        //then
        $this->assertEquals(array(
                '1' => array($product1),
                '2' => array($product3, $product2))
            , $grouped);
    }

    /**
     * @test
     */
    public function shouldSortArray()
    {
        //given
        $product1 = new Product(array('id_category' => '2'));
        $product2 = new Product(array('id_category' => '3'));
        $product3 = new Product(array('id_category' => '1'));
        $array = array($product1, $product2, $product3);

        //when
        $sorted = Arrays::orderBy($array, 'id_category');

        //then
        $this->assertEquals(array($product3, $product1, $product2), $sorted);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnSameArrayForArray()
    {
        // given
        $array = array(1, 2, 3);

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
        $this->assertEquals(array('test'), $result);
    }

    /**
     * @test
     */
    public function toArrayShouldReturnEmptyArrayForNull()
    {
        // when
        $result = Arrays::toArray(null);

        // then
        $this->assertEquals(array(), $result);
    }

    /**
     * @test
     */
    public function shouldGetRandomElement()
    {
        //given
        $array = array(1, 3, 6, 9);

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
        $array = array();

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
        $array = array('id' => 1, 'name' => 'john');

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
        $array = array('id' => 1, 'name' => 'john');

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
        $keys = array('id', 'name', 'surname');
        $values = array(1, 'john', 'smith');

        //when
        $combined = Arrays::combine($keys, $values);

        //then
        Assert::thatArray($combined)->hasSize(3)->containsKeyAndValue(array('id' => 1, 'name' => 'john', 'surname' => 'smith'));
    }

    /**
     * @test
     */
    public function shouldFlattenAnArray()
    {
        //given
        $array = array(
            'names' => array(
                'john',
                'peter',
                'bill'
            ),
            'products' => array(
                'cheese',
                'test' => array(
                    'natural' => 'milk',
                    'brie'
                )
            )
        );

        //when
        $flatten = Arrays::flatten($array);

        //then
        Assert::thatArray($flatten)->hasSize(6)->containsExactly('john', 'peter', 'bill', 'cheese', 'milk', 'brie');
    }

    /**
     * @test
     */
    public function shouldCheckIsKeyExists()
    {
        //given
        $array = array('id' => 1, 'name' => 'john');

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
        $array = array('$id', '$name', '$phone');

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
        $value = Arrays::find(array('a', 'b', 'c'), function ($element) {
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
        $value = Arrays::find(array('a', 'c'), function ($element) {
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
        $a1 = array('1', '4', '5');
        $a2 = array('1', '4', '6');

        //when
        $intersection = Arrays::intersect($a1, $a2);

        //then
        $this->assertEquals(array('1', '4'), $intersection);
    }

    /**
     * @test
     */
    public function shouldReturnNestedValue()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        $value = Arrays::getNestedValue($array, array('1', '2', '3'));

        //then
        $this->assertEquals('value', $value);
    }

    /**
     * @test
     */
    public function getNestedValueShouldReturnNullWhenKeyNotFound()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        $value = Arrays::getNestedValue($array, array('1', '4'));

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function shouldSetNestedValue()
    {
        //given
        $array = array();

        //when
        Arrays::setNestedValue($array, array('1', '2', '3'), 'value');

        //then
        $this->assertEquals(array('1' => array('2' => array('3' => 'value'))), $array);
    }

    /**
     * @test
     */
    public function shouldRemoveNestedKeyAtRoot()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        Arrays::removeNestedKey($array, array('1'));

        //then
        $this->assertEquals(array(), $array);
    }

    /**
     * @test
     */
    public function shouldRemoveNestedKey()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        Arrays::removeNestedKey($array, array('1', '2'));

        //then
        $this->assertEquals(array('1' => array()), $array);
    }

    /**
     * @test
     * @expectedException PHPUnit_Framework_Error_Deprecated
     * @expectedExceptionMessage Use Arrays::removeNestedKey instead
     */
    public function shouldThrowDeprecatedExceptionWhenUseRemoveNestedValue()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        Arrays::removeNestedValue($array, array('1', '2'));
    }

    /**
     * @test
     */
    public function shouldNotRemoveNestedKeyWhenKeyNotFound()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        Arrays::removeNestedKey($array, array('1', '4'));

        //then
        $this->assertEquals(array('1' => array('2' => array('3' => 'value'))), $array);
    }

    /**
     * @test
     */
    public function shouldCheckIfArrayHasNestedKey()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        $value = Arrays::hasNestedKey($array, array('1', '2', '3'));

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function shouldCheckIfArrayHasNestedKeyForRoot()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        $value = Arrays::hasNestedKey($array, array('1'));

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnFalseWhenKeyDoesNotExist()
    {
        //given
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        $value = Arrays::hasNestedKey($array, array('1', '4'));

        //then
        $this->assertFalse($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnTrueWhenKeyIsNullAndNullIsValue()
    {
        //given
        $array = array('1' => array('2' => array('3' => null)));

        //when
        $value = Arrays::hasNestedKey($array, array('1', '2', '3'), TREAT_NULL_AS_VALUE);

        //then
        $this->assertTrue($value);
    }

    /**
     * @test
     */
    public function hasNestedKeyShouldReturnFalseWhenKeyIsNullAndNullIsNotValue()
    {
        //given
        $array = array('1' => array('2' => array('3' => null)));

        //when
        $value = Arrays::hasNestedKey($array, array('1', '2', '3'));

        //then
        $this->assertFalse($value);
    }
    /**
     * @test
     */
    public function shouldFilterNotBlank()
    {
        //given
        $array = array(
            0 => 'foo',
            1 => false,
            2 => -1,
            3 => null,
            4 => ''
        );

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
        $array = array('1' => array('2' => array('3' => 'value')));

        //when
        $value = Arrays::getNestedValue($array, array('1', '2', '3', '4'));

        //then
        $this->assertNull($value);
    }
}