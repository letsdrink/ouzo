<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Joiner;

class JoinerTest extends PHPUnit_Framework_TestCase
{
    public function arrayJoinedWithSeparator()
    {
        return array(
            array(array('A', 'B', 'C'), 'A, B, C'),
            array(array('A'), 'A'),
            array(array('A', '', 'C'), 'A, , C'),
            array(array('A', null, 'C'), 'A, , C'),
            array(array('A', null), 'A'),
            array(array(''), ''),
            array(array(null), ''),
            array(array(), '')
        );
    }

    /**
     * @test
     * @dataProvider arrayJoinedWithSeparator
     * @param array $array
     * @param string $expectedResult
     */
    public function shouldJoinArrayWithSeparator($array, $expectedResult)
    {
        //when
        $result = Joiner::on(', ')->join($array);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function shouldJoinArrayWithEmptySeparator()
    {
        //when
        $result = Joiner::on('')->join(array('A', 'B', 'C'));

        //then
        $this->assertEquals('ABC', $result);
    }

    /**
     * @test
     */
    public function shouldJoinArrayAndSkipNulls()
    {
        //when
        $result = Joiner::on(', ')->skipNulls()->join(array('A', null, 'C'));

        //then
        $this->assertEquals('A, C', $result);
    }

    /**
     * @test
     */
    public function shouldJoinArrayAndSkipEmptyStringsOnSkipNulls()
    {
        //when
        $result = Joiner::on(', ')->skipNulls()->join(array('A', '', 'C'));

        //then
        $this->assertEquals('A, C', $result);
    }

    /**
     * @test
     */
    public function shouldJoinMap()
    {
        //when
        $result = Joiner::on(', ')->join(array(1 => 'A', 2 => 'B', 3 => 'C'));

        //then
        $this->assertEquals('A, B, C', $result);
    }

    /**
     * @test
     */
    public function shouldJoinArrayApplyingFunction()
    {
        //when
        $result = Joiner::on(', ')->map(function ($key, $value) {
            return strtolower($value);
        })->join(array('A', 'B', 'C'));

        //then
        $this->assertEquals('a, b, c', $result);
    }

    /**
     * @test
     */
    public function shouldJoinMapApplyingFunction()
    {
        //when
        $result = Joiner::on(', ')->map(function ($key, $value) {
            return "$key => $value";
        })->join(array(1 => 'A', 2 => 'B', 3 => 'C'));

        //then
        $this->assertEquals('1 => A, 2 => B, 3 => C', $result);
    }

    /**
     * @test
     */
    public function shouldJoinMapApplyingFunctionOnValues()
    {
        //when
        $result = Joiner::on(', ')->mapValues(function ($value) {
            return "val = $value";
        })->join(array(1 => 'A', 2 => 'B', 3 => 'C'));

        //then
        $this->assertEquals('val = A, val = B, val = C', $result);
    }
}
