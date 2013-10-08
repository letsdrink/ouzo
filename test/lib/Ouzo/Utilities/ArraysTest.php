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
                array('milk', 'brie')
            )
        );

        //when
        $flatten = Arrays::flatten($array);

        //then
        Assert::thatArray($flatten)->hasSize(6)->containsExactly('john', 'peter', 'bill', 'cheese', 'milk', 'brie');
    }
}