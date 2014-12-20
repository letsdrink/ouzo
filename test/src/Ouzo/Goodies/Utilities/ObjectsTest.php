<?php
use Ouzo\Utilities\Objects;

class ClassImplementingToString
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function __toString()
    {
        return $this->string;
    }
}

class ClassWithProperty
{
    public $property;
    private $privateProperty = 'private value';
}

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
        $result = Objects::getValueRecursively($object, 'field1->field2');

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
        $result = Objects::getValueRecursively($object, 'field1->field2', 'default');

        //then
        $this->assertEquals('default', $result);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultForNonExistentProperty()
    {
        //given
        $object = new ClassWithProperty();

        //when
        $result = Objects::getValueRecursively($object, 'NonExistentProperty', 'default');

        //then
        $this->assertEquals('default', $result);
    }

    /**
     * @test
     */
    public function shouldReturnValueForPrivatePropertyWhenFlagIsOn()
    {
        //given
        $object = new ClassWithProperty();

        //when
        $result = Objects::getValueRecursively($object, 'privateProperty', null, true);

        //then
        $this->assertEquals('private value', $result);
    }

    /**
     * @test
     */
    public function shouldReturnNullForPrivatePropertyWhenFlagIsOff()
    {
        //given
        $object = new ClassWithProperty();

        //when
        $result = Objects::getValueRecursively($object, 'privateProperty');

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldReturnValueOfExistingNotNullProperty()
    {
        //given
        $object = new ClassWithProperty();
        $object->property = 'prop';

        //when
        $result = Objects::getValueRecursively($object, 'property', 'default');

        //then
        $this->assertEquals('prop', $result);
    }

    /**
     * @test
     */
    public function getFieldRecursivelyShouldReturnNullWhenFieldNotFoundAndNoDefaultValueWasSpecified()
    {
        //given
        $object = new stdClass();

        //when
        $result = Objects::getValueRecursively($object, 'field1->field2');

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function getFieldRecursivelyShouldReturnNonNestedValue()
    {
        //given
        $object = new stdClass();
        $object->field1 = 'value';

        //when
        $result = Objects::getValueRecursively($object, 'field1');

        //then
        $this->assertEquals('value', $result);
    }

    /**
     * @test
     */
    public function shouldReturnObjectIfEmptyField()
    {
        //given
        $object = new stdClass();

        //when
        $result = Objects::getValueRecursively($object, '');

        //then
        $this->assertEquals($object, $result);
    }

    /**
     * @test
     */
    public function shouldSetValueRecursivelyForNonNestedField()
    {
        //given
        $object = new stdClass();

        //when
        Objects::setValueRecursively($object, 'field1', 'value');

        //then
        $this->assertEquals('value', $object->field1);
    }

    /**
     * @test
     */
    public function shouldSetValueRecursivelyForNestedField()
    {
        //given
        $object = new stdClass();
        $object->field1 = new stdClass();

        //when
        Objects::setValueRecursively($object, 'field1->field2', 'value');

        //then
        $this->assertEquals('value', $object->field1->field2);
    }

    /**
     * @test
     */
    public function shouldSetValueRecursivelyForNonExistentNestedField()
    {
        //given
        $object = new stdClass();

        //when
        Objects::setValueRecursively($object, 'field1->field2', 'value');

        //then
        $this->assertFalse(isset($object->field1));
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
        $this->assertEquals('stdClass {<field1> => "field1", <field2> => "field2"}', $stringifyObject);
    }

    /**
     * @test
     */
    public function shouldStringifyObjectWithToString()
    {
        //given
        $object = new ClassImplementingToString("string");

        //when
        $stringifiedObject = Objects::toString($object);

        //then
        $this->assertEquals('string', $stringifiedObject);
    }

    /**
     * @test
     */
    public function shouldReturnStringWhenNoMatchedTypes()
    {
        //given
        $int = 1;

        //when
        $string = Objects::toString($int);

        //then
        $this->assertSame('1', $string);
    }
}
