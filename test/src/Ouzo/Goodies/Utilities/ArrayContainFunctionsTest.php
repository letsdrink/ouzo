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
    public function containsShouldForArraysValues()
    {
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array(1)));
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array(2)));
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array('1')));
        $this->assertTrue(ArrayContainFunctions::contains(array(array(1), array(2)), array('2')));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), 1));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), '1'));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), null));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array(true)));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array(null)));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array()));
        $this->assertFalse(ArrayContainFunctions::contains(array(array(1), array(2)), array(new stdClass())));
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
