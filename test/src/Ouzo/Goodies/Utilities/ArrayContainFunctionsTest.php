<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\ArrayContainFunctions;

class ArrayContainFunctionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function containsShouldForEmptyArrays()
    {
        $this->assertFalse(ArrayContainFunctions::contains(array(), array()));
        $this->assertFalse(ArrayContainFunctions::contains(array(), null));
        $this->assertFalse(ArrayContainFunctions::contains(array(), true));
        $this->assertFalse(ArrayContainFunctions::contains(array(), false));
        $this->assertFalse(ArrayContainFunctions::contains(array(), 0));
        $this->assertFalse(ArrayContainFunctions::contains(array(), 1));
        $this->assertFalse(ArrayContainFunctions::contains(array(), '0'));
        $this->assertFalse(ArrayContainFunctions::contains(array(), '1'));
        $this->assertFalse(ArrayContainFunctions::contains(array(), new stdClass()));
    }

    /**
     * @test
     */
    public function containsShouldForBooleans()
    {
        $this->assertTrue(ArrayContainFunctions::contains(array(true, 3), true));
        $this->assertTrue(ArrayContainFunctions::contains(array(true, 3), 'true'));
        $this->assertTrue(ArrayContainFunctions::contains(array(true, 3), 3));
        $this->assertTrue(ArrayContainFunctions::contains(array(true, 3), '3'));
        $this->assertFalse(ArrayContainFunctions::contains(array(true, 3), null));
        $this->assertFalse(ArrayContainFunctions::contains(array(true, 3), 1));
        $this->assertFalse(ArrayContainFunctions::contains(array(true, 3), '1'));
        $this->assertFalse(ArrayContainFunctions::contains(array(true, 3), false));
        $this->assertFalse(ArrayContainFunctions::contains(array(true, 3), new stdClass()));

        $this->assertTrue(ArrayContainFunctions::contains(array(false, 3), false));
        $this->assertTrue(ArrayContainFunctions::contains(array(false, 3), 'false'));
        $this->assertTrue(ArrayContainFunctions::contains(array(false, 3), 3));
        $this->assertTrue(ArrayContainFunctions::contains(array(false, 3), '3'));
        $this->assertFalse(ArrayContainFunctions::contains(array(false, 3), null));
        $this->assertFalse(ArrayContainFunctions::contains(array(false, 3), 0));
        $this->assertFalse(ArrayContainFunctions::contains(array(false, 3), '0'));
        $this->assertFalse(ArrayContainFunctions::contains(array(false, 3), true));
        $this->assertFalse(ArrayContainFunctions::contains(array(false, 3), new stdClass()));
    }

    /**
     * @test
     */
    public function containsShouldForNull()
    {
        $this->assertTrue(ArrayContainFunctions::contains(array(null, 1), null));
        $this->assertTrue(ArrayContainFunctions::contains(array(null, 1), 1));
        $this->assertTrue(ArrayContainFunctions::contains(array(null, 1), '1'));
        $this->assertFalse(ArrayContainFunctions::contains(array(null, 1), true));
        $this->assertFalse(ArrayContainFunctions::contains(array(null, 1), false));
        $this->assertFalse(ArrayContainFunctions::contains(array(null, 1), 0));
        $this->assertFalse(ArrayContainFunctions::contains(array(null, 1), ''));
        $this->assertFalse(ArrayContainFunctions::contains(array(null, 1), new stdClass()));
    }

    /**
     * @test
     */
    public function containsShouldForPrimitives()
    {
        $this->assertTrue(ArrayContainFunctions::contains(array(1, 2, 3), 1));
        $this->assertTrue(ArrayContainFunctions::contains(array(1, 2, 3), 2));
        $this->assertTrue(ArrayContainFunctions::contains(array(1, 2, 3), 3));
        $this->assertTrue(ArrayContainFunctions::contains(array(1, 2, 3), '1'));
        $this->assertTrue(ArrayContainFunctions::contains(array(1, 2, 3), '2'));
        $this->assertTrue(ArrayContainFunctions::contains(array(1, 2, 3), '3'));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), 0));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), 4));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), '5'));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), '11'));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), true));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), false));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), null));
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2, 3), new stdClass()));
    }

    /**
     * @test
     */
    public function containsAllShouldWorkForArraysValues()
    {
        $this->assertTrue(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(array(1))));
        $this->assertTrue(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(array(2))));
        $this->assertTrue(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(array('1'))));
        $this->assertTrue(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(array('2'))));

        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(1)));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(2)));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array('1')));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array('2')));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), 1));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), '1'));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), null));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(true)));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(null)));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array()));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(new stdClass())));

        $this->assertTrue(ArrayContainFunctions::containsAll(array(array('1' => 'a', '2' => 'b'), array('1' => 'c', '2' => 'd')), array(array('1' => 'a', '2' => 'b'))));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array('a', 'b'), array('c', 'd')), array('a', 'd')));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array('1' => 'a', '2' => 'b'), array('1' => 'c', '2' => 'd')), array('x' => 'a', 'y' => 'd')));
    }

    /**
     * @test
     */
    public function containsShouldForArraysValues()
    {
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array(1)));
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array(2)));
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array('1')));
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array('2')));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), 1));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), '1'));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), null));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array(null)));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array()));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array(new stdClass())));

        $this->assertTrue(ArrayContainFunctions::contains(array(array('1' => 'a', '2' => 'b'), array('1' => 'c', '2' => 'd')), array('1' => 'a', '2' => 'b')));
        $this->assertFalse(ArrayContainFunctions::contains(array(array('a', 'b'), array('c', 'd')), array('a', 'd')));
        $this->assertFalse(ArrayContainFunctions::contains(array(array('1' => 'a', '2' => 'b'), array('1' => 'c', '2' => 'd')), array('x' => 'a', 'y' => 'd')));
    }

    /**
     * @test
     */
    public function containsShouldNotTreatOneAsTrue()
    {
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array(true)));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(1), array(2)), array(true)));
    }

    /**
     * @test
     */
    public function containsShouldNotTreatNumbersAsTrue()
    {
        $this->assertFalse(ArrayContainFunctions::contains(array(array(3), array(2)), array(true)));
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array(3), array(2)), array(true)));
    }

    /**
     * @test
     */
    public function containsShouldBeSymmetric()
    {
        $this->assertEquals(
            ArrayContainFunctions::contains(array(true), 1),
            ArrayContainFunctions::contains(array(1), true)
        );
    }

    /**
     * @test
     */
    public function containsShouldBeSymmetricForArrays()
    {
        $this->assertEquals(
            ArrayContainFunctions::contains(array(array(true)), array(1)),
            ArrayContainFunctions::contains(array(array(1)), array(true))
        );
    }

    /**
     * @test
     */
    public function containsShouldNotTreatOneAsTrue1()
    {
        $this->assertFalse(ArrayContainFunctions::contains(array(1, 2), true));
    }

    /**
     * @test
     */
    public function containsAllShouldCompareWholeElements()
    {
        $this->assertFalse(ArrayContainFunctions::containsAll(array(array('a', 'b'), array('c', 'd')), array('a', 'd')));
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
        $this->assertTrue(ArrayContainFunctions::contains(array(new stdClass()), new stdClass()));
        $this->assertTrue(ArrayContainFunctions::contains(array($a, $b), $a));
        $this->assertTrue(ArrayContainFunctions::contains(array($a, $b), $b));
        $this->assertTrue(ArrayContainFunctions::contains(array($a, $b), $c));
        $this->assertFalse(ArrayContainFunctions::contains(array($a, $b), new stdClass()));
    }
}
