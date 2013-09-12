<?php

use Ouzo\Utilities\Objects;

class ObjectsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldExtractFieldsRecursively()
    {
        //given
        $object = new stdClass();
        $object->field1 = new stdClass();
        $object->field1->field2 = 'value';

        //when
        $result = Objects::getFieldRecursively($object, 'field1->field2');

        //then
        $this->assertEquals('value', $result);
    }

    /**
     * @test
     */
    public function getFieldRecursivelyShouldReturnDefaultValueWhenFieldNotFound()
    {
        //given
        $object = new stdClass();

        //when
        $result = Objects::getFieldRecursively($object, 'field1->field2', 'default');

        //then
        $this->assertEquals('default', $result);
    }

    /**
     * @test
     */
    public function getFieldRecursivelyShouldReturnNullWhenFieldNotFoundAndNoDefaultValueWasSpecified()
    {
        //given
        $object = new stdClass();

        //when
        $result = Objects::getFieldRecursively($object, 'field1->field2');

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldStringifyBool()
    {
        //given
        $boolValue = true;

        //when
        $stringifyBool = Objects::toString($boolValue);

        //then
        $this->assertEquals('true', $stringifyBool);
    }

    /**
     * @test
     */
    public function shouldStringifyNull()
    {
        //given
        $null = null;

        //when
        $stringifyNull = Objects::toString($null);

        //then
        $this->assertEquals('null', $stringifyNull);
    }

    /**
     * @test
     */
    public function shouldStringifyString()
    {
        //given
        $string = 'string';

        //when
        $stringifyString = Objects::toString($string);

        //then
        $this->assertEquals('"string"', $stringifyString);
    }

    /**
     * @test
     */
    public function shouldStringifyInt()
    {
        //given
        $int = 1;

        //when
        $stringifyInt = Objects::toString($int);

        //then
        $this->assertEquals('1', $stringifyInt);
    }

    /**
     * @test
     */
    public function shouldStringifyNotAssociativeArray()
    {
        //given
        $notAssociativeArray = array('a', 1);

        //when
        $stringifyArray = Objects::toString($notAssociativeArray);

        //then
        $this->assertEquals('["a", 1]', $stringifyArray);
    }

    /**
     * @test
     */
    public function shouldStringifyAssociativeArray()
    {
        //given
        $associativeArray = array('key' => 'value1', 'key2' => 'value2');

        //when
        $stringifyArray = Objects::toString($associativeArray);

        //then
        $this->assertEquals('[<key> => "value1", <key2> => "value2"]', $stringifyArray);
    }

    /**
     * @test
     */
    public function shouldStringifyObject()
    {
        //given
        $object = new stdClass();
        $object->field1 = 'field1';
        $object->field2 = 'field2';

        //when
        $stringifyObject = Objects::toString($object);

        //then
        $this->assertEquals('{<field1> => "field1", <field2> => "field2"}', $stringifyObject);
    }

    /**
     * @test
     */
    public function shouldReturnStringWhenNotMatchTypes()
    {
        //given
        $int = 1;

        //when
        $trySingifyInt = Objects::toString($int);

        //then
        $this->assertSame('1', $trySingifyInt);
    }
}