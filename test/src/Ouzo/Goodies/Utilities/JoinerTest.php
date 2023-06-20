<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Joiner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class JoinerTest extends TestCase
{
    #[Test]
    #[DataProvider('arrayJoinedWithSeparator')]
    public function shouldJoinArrayWithSeparator(array $array, string $expectedResult): void
    {
        //when
        $result = Joiner::on(', ')->join($array);

        //then
        $this->assertEquals($expectedResult, $result);
    }

    public static function arrayJoinedWithSeparator(): array
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

    #[Test]
    public function shouldJoinArrayWithEmptySeparator()
    {
        //when
        $result = Joiner::on('')->join(['A', 'B', 'C']);

        //then
        $this->assertEquals('ABC', $result);
    }

    #[Test]
    public function shouldJoinArrayAndSkipNulls()
    {
        //when
        $result = Joiner::on(', ')->skipNulls()->join(['A', null, 'C']);

        //then
        $this->assertEquals('A, C', $result);
    }

    #[Test]
    public function shouldJoinArrayAndSkipEmptyStringsOnSkipNulls()
    {
        //when
        $result = Joiner::on(', ')->skipNulls()->join(['A', '', 'C']);

        //then
        $this->assertEquals('A, C', $result);
    }

    #[Test]
    public function shouldJoinMap()
    {
        //when
        $result = Joiner::on(', ')->join([1 => 'A', 2 => 'B', 3 => 'C']);

        //then
        $this->assertEquals('A, B, C', $result);
    }

    #[Test]
    public function shouldJoinArrayApplyingFunction()
    {
        //when
        $result = Joiner::on(', ')
            ->map(fn($key, $value) => strtolower($value))
            ->join(['A', 'B', 'C']);

        //then
        $this->assertEquals('a, b, c', $result);
    }

    #[Test]
    public function shouldJoinMapApplyingFunction()
    {
        //when
        $result = Joiner::on(', ')
            ->map(fn($key, $value) => "$key => $value")
            ->join([1 => 'A', 2 => 'B', 3 => 'C']);

        //then
        $this->assertEquals('1 => A, 2 => B, 3 => C', $result);
    }

    #[Test]
    public function shouldJoinMapApplyingFunctionOnValues()
    {
        //when
        $result = Joiner::on(', ')
            ->mapValues(fn($value) => "val = $value")
            ->join([1 => 'A', 2 => 'B', 3 => 'C']);

        //then
        $this->assertEquals('val = A, val = B, val = C', $result);
    }
}
