<?php


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
}