<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Booleans;

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
        $this->assertEquals($expected, $toBoolean);
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
            array('x gti', false)
        );
    }
}
