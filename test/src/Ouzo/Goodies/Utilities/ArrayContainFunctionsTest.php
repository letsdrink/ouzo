<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\ArrayContainFunctions;

use PHPUnit\Framework\TestCase; 

class ArrayContainFunctionsTest extends TestCase
{
    /**
     * @test
     */
    public function containsShouldForEmptyArrays()
    {
        $this->assertFalse(ArrayContainFunctions::contains([], []));
        $this->assertFalse(ArrayContainFunctions::contains([], null));
        $this->assertFalse(ArrayContainFunctions::contains([], true));
        $this->assertFalse(ArrayContainFunctions::contains([], false));
        $this->assertFalse(ArrayContainFunctions::contains([], 0));
        $this->assertFalse(ArrayContainFunctions::contains([], 1));
        $this->assertFalse(ArrayContainFunctions::contains([], '0'));
        $this->assertFalse(ArrayContainFunctions::contains([], '1'));
        $this->assertFalse(ArrayContainFunctions::contains([], new stdClass()));
    }

    /**
     * @test
     */
    public function containsShouldForBooleans()
    {
        $this->assertTrue(ArrayContainFunctions::contains([true, 3], true));
        $this->assertTrue(ArrayContainFunctions::contains([true, 3], 'true'));
        $this->assertTrue(ArrayContainFunctions::contains([true, 3], 3));
        $this->assertTrue(ArrayContainFunctions::contains([true, 3], '3'));
        $this->assertFalse(ArrayContainFunctions::contains([true, 3], null));
        $this->assertFalse(ArrayContainFunctions::contains([true, 3], 1));
        $this->assertFalse(ArrayContainFunctions::contains([true, 3], '1'));
        $this->assertFalse(ArrayContainFunctions::contains([true, 3], false));
        $this->assertFalse(ArrayContainFunctions::contains([true, 3], new stdClass()));

        $this->assertTrue(ArrayContainFunctions::contains([false, 3], false));
        $this->assertTrue(ArrayContainFunctions::contains([false, 3], 'false'));
        $this->assertTrue(ArrayContainFunctions::contains([false, 3], 3));
        $this->assertTrue(ArrayContainFunctions::contains([false, 3], '3'));
        $this->assertFalse(ArrayContainFunctions::contains([false, 3], null));
        $this->assertFalse(ArrayContainFunctions::contains([false, 3], 0));
        $this->assertFalse(ArrayContainFunctions::contains([false, 3], '0'));
        $this->assertFalse(ArrayContainFunctions::contains([false, 3], true));
        $this->assertFalse(ArrayContainFunctions::contains([false, 3], new stdClass()));
    }

    /**
     * @test
     */
    public function containsShouldForNull()
    {
        $this->assertTrue(ArrayContainFunctions::contains([null, 1], null));
        $this->assertTrue(ArrayContainFunctions::contains([null, 1], 1));
        $this->assertTrue(ArrayContainFunctions::contains([null, 1], '1'));
        $this->assertFalse(ArrayContainFunctions::contains([null, 1], true));
        $this->assertFalse(ArrayContainFunctions::contains([null, 1], false));
        $this->assertFalse(ArrayContainFunctions::contains([null, 1], 0));
        $this->assertFalse(ArrayContainFunctions::contains([null, 1], ''));
        $this->assertFalse(ArrayContainFunctions::contains([null, 1], new stdClass()));
    }

    /**
     * @test
     */
    public function containsShouldForPrimitives()
    {
        $this->assertTrue(ArrayContainFunctions::contains([1, 2, 3], 1));
        $this->assertTrue(ArrayContainFunctions::contains([1, 2, 3], 2));
        $this->assertTrue(ArrayContainFunctions::contains([1, 2, 3], 3));
        $this->assertTrue(ArrayContainFunctions::contains([1, 2, 3], '1'));
        $this->assertTrue(ArrayContainFunctions::contains([1, 2, 3], '2'));
        $this->assertTrue(ArrayContainFunctions::contains([1, 2, 3], '3'));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], 0));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], 4));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], '5'));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], '11'));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], true));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], false));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], null));
        $this->assertFalse(ArrayContainFunctions::contains([1, 2, 3], new stdClass()));
    }

    /**
     * @test
     */
    public function containsAllShouldWorkForArraysValues()
    {
        $this->assertTrue(ArrayContainFunctions::containsAll([[1], [2]], [[1]]));
        $this->assertTrue(ArrayContainFunctions::containsAll([[1], [2]], [[2]]));
        $this->assertTrue(ArrayContainFunctions::containsAll([[1], [2]], [['1']]));
        $this->assertTrue(ArrayContainFunctions::containsAll([[1], [2]], [['2']]));

        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], [1]));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], [2]));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], ['1']));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], ['2']));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], 1));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], '1'));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], null));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], [true]));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], [null]));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], []));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], [new stdClass()]));

        $this->assertTrue(ArrayContainFunctions::containsAll([['1' => 'a', '2' => 'b'], ['1' => 'c', '2' => 'd']], [['1' => 'a', '2' => 'b']]));
        $this->assertFalse(ArrayContainFunctions::containsAll([['a', 'b'], ['c', 'd']], ['a', 'd']));
        $this->assertFalse(ArrayContainFunctions::containsAll([['1' => 'a', '2' => 'b'], ['1' => 'c', '2' => 'd']], ['x' => 'a', 'y' => 'd']));
    }

    /**
     * @test
     */
    public function containsShouldForArraysValues()
    {
        $this->assertTrue(ArrayContainFunctions::contains([[1], [2]], [1]));
        $this->assertTrue(ArrayContainFunctions::contains([[1], [2]], [2]));
        $this->assertTrue(ArrayContainFunctions::contains([[1], [2]], ['1']));
        $this->assertTrue(ArrayContainFunctions::contains([[1], [2]], ['2']));
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], 1));
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], '1'));
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], null));
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], [null]));
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], []));
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], [new stdClass()]));

        $this->assertTrue(ArrayContainFunctions::contains([['1' => 'a', '2' => 'b'], ['1' => 'c', '2' => 'd']], ['1' => 'a', '2' => 'b']));
        $this->assertFalse(ArrayContainFunctions::contains([['a', 'b'], ['c', 'd']], ['a', 'd']));
        $this->assertFalse(ArrayContainFunctions::contains([['1' => 'a', '2' => 'b'], ['1' => 'c', '2' => 'd']], ['x' => 'a', 'y' => 'd']));
    }

    /**
     * @test
     */
    public function containsShouldNotTreatOneAsTrue()
    {
        $this->assertFalse(ArrayContainFunctions::contains([[1], [2]], [true]));
        $this->assertFalse(ArrayContainFunctions::containsAll([[1], [2]], [true]));
    }

    /**
     * @test
     */
    public function containsShouldNotTreatNumbersAsTrue()
    {
        $this->assertFalse(ArrayContainFunctions::contains([[3], [2]], [true]));
        $this->assertFalse(ArrayContainFunctions::containsAll([[3], [2]], [true]));
    }

    /**
     * @test
     */
    public function containsShouldBeSymmetric()
    {
        $this->assertEquals(
            ArrayContainFunctions::contains([true], 1),
            ArrayContainFunctions::contains([1], true)
        );
    }

    /**
     * @test
     */
    public function containsShouldBeSymmetricForArrays()
    {
        $this->assertEquals(
            ArrayContainFunctions::contains([[true]], [1]),
            ArrayContainFunctions::contains([[1]], [true])
        );
    }

    /**
     * @test
     */
    public function containsShouldNotTreatOneAsTrue1()
    {
        $this->assertFalse(ArrayContainFunctions::contains([1, 2], true));
    }

    /**
     * @test
     */
    public function containsAllShouldCompareWholeElements()
    {
        $this->assertFalse(ArrayContainFunctions::containsAll([['a', 'b'], ['c', 'd']], ['a', 'd']));
    }

    /**
     * @test
     */
    public function containsShouldForObjectsValues()
    {
        $a = new stdClass();
        $a->var = 1;
        $b = new stdClass();
        $b->var = 2;
        $c = new stdClass();
        $c->var = '1';
        $this->assertTrue(ArrayContainFunctions::contains([new stdClass()], new stdClass()));
        $this->assertTrue(ArrayContainFunctions::contains([$a, $b], $a));
        $this->assertTrue(ArrayContainFunctions::contains([$a, $b], $b));
        $this->assertTrue(ArrayContainFunctions::contains([$a, $b], $c));
        $this->assertFalse(ArrayContainFunctions::contains([$a, $b], new stdClass()));
    }
}
