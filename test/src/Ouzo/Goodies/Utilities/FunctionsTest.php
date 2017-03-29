<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Utilities;

use Application\Model\Test\Product;
use stdClass;

class FunctionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldExtractId()
    {
        //given
        $product = new Product(['id' => 1]);

        //when
        $id = Functions::call(Functions::extractId(), $product);

        //then
        $this->assertEquals(1, $id);
    }

    /**
     * @test
     */
    public function shouldTrimWhiteCharacters()
    {
        //given
        $string = " snow ";

        //when
        $trimmed = Functions::call(Functions::trim(), $string);

        //then
        $this->assertEquals('snow', $trimmed);
    }

    /**
     * @test
     */
    public function shouldNegatePredicate()
    {
        $this->assertFalse(Functions::call(Functions::not(Functions::identity()), true));
        $this->assertTrue(Functions::call(Functions::not(Functions::identity()), false));
    }

    /**
     * @test
     */
    public function shouldTestIfArgumentIsArray()
    {
        $this->assertFalse(Functions::call(Functions::isArray(), 'string'));
        $this->assertTrue(Functions::call(Functions::isArray(), []));
    }

    /**
     * @test
     */
    public function shouldPrependPrefixToArgument()
    {
        //given
        $string = "snow";

        //when
        $prefixed = Functions::call(Functions::prepend('white '), $string);

        //then
        $this->assertEquals('white snow', $prefixed);
    }

    /**
     * @test
     */
    public function shouldAppendPostfixToArgument()
    {
        //given
        $string = "white";

        //when
        $modified = Functions::call(Functions::append(' snow'), $string);

        //then
        $this->assertEquals('white snow', $modified);
    }

    /**
     * @test
     */
    public function shouldComposeFunctions()
    {
        //given
        $functionA = Functions::trim();
        $functionB = Functions::append('a ');

        //when
        $result = Functions::call(Functions::compose($functionA, $functionB), ' ');

        //then
        $this->assertEquals('a', $result);
    }

    /**
     * @test
     */
    public function shouldSurroundStringWithGivenCharacter()
    {
        //when
        $result = Functions::call(Functions::surroundWith('.'), 'test');

        //then
        $this->assertEquals('.test.', $result);
    }

    /**
     * @test
     */
    public function shouldCheckIfParameterIsEqualToValue()
    {
        $this->assertFalse(Functions::call(Functions::equals('value'), 'other'));
        $this->assertTrue(Functions::call(Functions::equals('value'), 'value'));
    }

    /**
     * @test
     */
    public function shouldCheckIfParameterIsNotEqualToValue()
    {
        $this->assertTrue(Functions::call(Functions::notEquals('value'), 'other'));
        $this->assertFalse(Functions::call(Functions::notEquals('value'), 'value'));
    }

    /**
     * @test
     */
    public function shouldCheckIfParameterIsInstanceOfGivenType()
    {
        $this->assertTrue(Functions::call(Functions::isInstanceOf('stdClass'), new \stdClass()));
        $this->assertFalse(Functions::call(Functions::isInstanceOf('stdClass'), 'value'));
    }

    /**
     * @test
     */
    public function extractFieldRecursivelyShouldReturnNullWhenFieldNotFoundAndNoDefaultValueWasSpecified()
    {
        //given
        $object = new stdClass();

        //when
        $result = Functions::call(Functions::extractFieldRecursively('field1->field2'), $object);

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldExtractFieldIfPhpFunctionWithTheSameNameExists()
    {
        //given
        $object = new stdClass();
        $object->date = '2012-05-12';

        //when
        $result = Functions::call(Functions::extractExpression('date'), $object);

        //then
        $this->assertEquals($object->date, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateRandomNumber()
    {
        //given
        $function = Functions::random();

        //when
        $result = $function();

        //then
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(getrandmax(), $result);
    }

    /**
     * @test
     */
    public function shouldGenerateRandomNumberInRange()
    {
        for ($i = 0; $i < 100; ++$i) {
            //given
            $function = Functions::random(3, 7);

            //when
            $result = $function();

            //then
            $this->assertGreaterThanOrEqual(3, $result);
            $this->assertLessThanOrEqual(7, $result);
        }
    }

    /**
     * @test
     */
    public function shouldCheckIsInArray()
    {
        //given
        $array = ['white', 'snow'];

        //when
        $result = Functions::call(Functions::inArray($array), 'snow');

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldCheckIsNotInArray()
    {
        //given
        $array = ['white', 'snow'];

        //when
        $result = Functions::call(Functions::notInArray($array), 'missing');

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldNotEqualUseTypeCheck()
    {
        //then
        $this->assertTrue(Functions::call(Functions::notEquals('value'), 0));
    }

    /**
     * @test
     */
    public function shouldEqualUseTypeCheck()
    {
        //then
        $this->assertFalse(Functions::call(Functions::equals('value'), 0));
    }

    /**
     * @test
     */
    public function shouldCheckNotNull()
    {
        //then
        $this->assertTrue(Functions::call(Functions::notNull(), 1));
    }

    /**
     * @test
     */
    public function shouldCheckNull()
    {
        //then
        $this->assertFalse(Functions::call(Functions::notNull(), null));
    }
}
