<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Booleans;
use Ouzo\Utilities\Objects;

class BooleansTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider toBoolean
     * @param $string
     * @param $expected
     */
    public function shouldConvertToBoolean($string, $expected)
    {
        //when
        $toBoolean = Booleans::toBoolean($string);

        //then
        $this->assertEquals($expected, $toBoolean, 'To convert: ' . Objects::toString($string) . ' Expected: ' . Objects::toString($expected));
    }

    public function toBoolean()
    {
        return array(
            array(true, true),
            array(null, false),
            array('true', true),
            array('TRUE', true),
            array('tRUe', true),
            array('on', true),
            array('yes', true),
            array('false', false),
            array('x gti', false),
            array('0', false),
            array('1', true),
            array('2', true),
            array(0, false),
            array(1, true),
            array(2, true)
        );
    }
}
