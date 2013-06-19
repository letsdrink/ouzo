<?php
use Thulium\Utilities\FluentArray;

class FluentArrayTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSupportChaining()
    {
        //given
        $array = array(
            1 => 2,
            2 => 3,
            3 => 3
        );

        //when
        $transformed = FluentArray::from($array)
            ->values()
            ->filter(function ($item) {
                return $item > 2;
            })
            ->unique()
            ->values()
            ->toArray();

        //then
        $this->assertEquals(array(3), $transformed);
    }

    /**
     * @test
     */
    public function shouldReturnArrayKeys()
    {
        //given
        $array = array(1 => 'a', 2 => 'b', 3 => 'c');

        //when
        $transformed = FluentArray::from($array)->keys()->toArray();

        //then
        $this->assertEquals(array_keys($array), $transformed);
    }
}