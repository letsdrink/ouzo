<?php
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;

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

    /**
     * @test
     */
    public function shouldFlattenArray()
    {
        //given
        $array = array(array(1, 2), array(3, 4));

        //when
        $flattened = FluentArray::from($array)->flatten()->toArray();

        //then
        $this->assertEquals(array(1, 2, 3, 4), $flattened);
    }

    /**
     * @test
     */
    public function shouldConvertToMap()
    {
        //given
        $obj[0] = new stdClass();
        $obj[0]->field1 = 'key1';
        $obj[0]->field2 = 'value1';
        $obj[1] = new stdClass();
        $obj[1]->field1 = 'key2';
        $obj[1]->field2 = 'value2';

        //when
        $toMap = FluentArray::from($obj)->toMap(Functions::extractField('field1'), Functions::extractField('field2'))->toArray();

        //then
        $this->assertEquals(array('key1' => 'value1', 'key2' => 'value2'), $toMap);
    }

    /**
     * @test
     */
    public function shouldConvertToJson()
    {
        //given
        $array = array(1 => 'a', 2 => 'b', 3 => 'c');

        //when
        $json = FluentArray::from($array)->toJson();

        //then
        $this->assertEquals('{"1":"a","2":"b","3":"c"}', $json);
    }
}