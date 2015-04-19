<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Functions;

class ExtractorTestClass
{
    public function returnArgument($type)
    {
        return $type;
    }
}

class ExtractorTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldExtractField()
    {
        //given
        $function = Functions::extract()->name;

        //when
        $result = Functions::call($function, new Product(array('name' => 'bmw')));

        //then
        Assert::thatString($result)->isEqualTo('bmw');
    }

    /**
     * @test
     */
    public function shouldExtractFieldWithZero()
    {
        //given
        $function = Functions::extract()->id;

        //when
        $result = Functions::call($function, new Product(array('id' => 0)));

        //then
        $this->assertTrue($result === 0);
    }

    /**
     * @test
     */
    public function shouldExtractNestedField()
    {
        //given
        $object = new stdClass();
        $object->field1 = new stdClass();
        $object->field1->field2 = 'value';

        $function = Functions::extract()->field1->field2;

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::thatString($result)->isEqualTo('value');
    }

    /**
     * @test
     */
    public function shouldExtractNestedMethod()
    {
        //given
        $object = new stdClass();
        $object->field1 = new stdClass();
        $object->field1->product = new Product();

        $function = Functions::extract()->field1->product->getDescription();

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::thatString($result)->isEqualTo('This is product,');
    }

    /**
     * @test
     */
    public function shouldExtractFieldAfterMethod()
    {
        //given
        $product = new Product();
        $product->category = new Category(array('name' => 'category'));

        $function = Functions::extract()->getCategory()->name;

        //when
        $result = Functions::call($function, $product);

        //then
        Assert::thatString($result)->isEqualTo('category');
    }

    /**
     * @test
     */
    public function shouldBindMethodParameters()
    {
        //given
        $object = new ExtractorTestClass();
        $function = Functions::extract()->returnArgument('argument');

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::thatString($result)->isEqualTo('argument');
    }

    /**
     * @test
     */
    public function shouldReturnNull()
    {
        //given
        $object = new stdClass();

        $function = Functions::extract()->field1->field2;

        //when
        $result = Functions::call($function, $object);

        //then
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function shouldAcceptTypeAsFirstParameter()
    {
        //given
        $product = new Product();
        $product->category = new Category(array('name' => 'category'));

        //PhpStorm with dynamicReturnType plugin will complete all methods/properties
        $function = Functions::extract('Model\Test\Product')->category->name;

        //when
        $result = Functions::call($function, $product);

        //then
        Assert::thatString($result)->isEqualTo('category');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoOperationGiven()
    {
        //given
        $function = Functions::extract();

        //when
        try {
            Functions::call($function, new Product());
            $this->fail();
        } //then
        catch (InvalidArgumentException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldExtractArrayValueFromField()
    {
        //given
        $object = new stdClass();
        $object->field1 = array('key' => 'value');

        $function = Functions::extract()->field1['key'];

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::thatString($result)->isEqualTo('value');
    }

    /**
     * @test
     */
    public function shouldExtractArrayValue()
    {
        //given
        $object = array('key' => 'value');

        //$function = Functions::extract()['key']; only i php 5.4
        $function = Functions::extract()->offsetGet('key');

        //when
        $result = Functions::call($function, $object);

        //then
        Assert::thatString($result)->isEqualTo('value');
    }
}
