<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Joiner;

use PHPUnit\Framework\TestCase; 

class JoinerTest extends TestCase
{
    public function arrayJoinedWithSeparator()
    {
        return [
            [['A', 'B', 'C'], 'A, B, C'],
            [['A'], 'A'],
            [['A', '', 'C'], 'A, , C'],
            [['A', null, 'C'], 'A, , C'],
            [['A', null], 'A'],
            [[''], ''],
            [[null], ''],
            [[], '']
        ];
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
        $result = Joiner::on('')->join(['A', 'B', 'C']);

        //then
        $this->assertEquals('ABC', $result);
    }

    /**
     * @test
     */
    public function shouldJoinArrayAndSkipNulls()
    {
        //when
        $result = Joiner::on(', ')->skipNulls()->join(['A', null, 'C']);

        //then
        $this->assertEquals('A, C', $result);
    }

    /**
     * @test
     */
    public function shouldJoinArrayAndSkipEmptyStringsOnSkipNulls()
    {
        //when
        $result = Joiner::on(', ')->skipNulls()->join(['A', '', 'C']);

        //then
        $this->assertEquals('A, C', $result);
    }

    /**
     * @test
     */
    public function shouldJoinMap()
    {
        //when
        $result = Joiner::on(', ')->join([1 => 'A', 2 => 'B', 3 => 'C']);

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
        })->join(['A', 'B', 'C']);

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
        })->join([1 => 'A', 2 => 'B', 3 => 'C']);

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
        })->join([1 => 'A', 2 => 'B', 3 => 'C']);

        //then
        $this->assertEquals('val = A, val = B, val = C', $result);
    }
}
