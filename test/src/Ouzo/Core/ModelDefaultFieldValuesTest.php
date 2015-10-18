<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\ProductWithDefaults;
use Ouzo\Tests\DbTransactionalTestCase;

class ModelDefaultFieldValuesTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldUseDefaultValueWhenCreatingNewInstance()
    {
        //when
        $product = new ProductWithDefaults();

        //then
        $this->assertEquals('no desc', $product->description);
        $this->assertEquals('no name', $product->name);
    }

    /**
     * @test
     */
    public function shouldOverrideDefaultValueWithAttributesWhenCreatingNewInstance()
    {
        //when
        $product = new ProductWithDefaults(array('description' => 'overridden desc', 'name' => 'overridden name'));

        //then
        $this->assertEquals('overridden desc', $product->description);
        $this->assertEquals('overridden name', $product->name);
    }

    /**
     * @test
     */
    public function createShouldUseDefaultValue()
    {
        //when
        $product = ProductWithDefaults::create();

        //then
        $this->assertEquals('no desc', $product->description);
        $this->assertEquals('no name', $product->name);
    }

    /**
     * @test
     */
    public function createShouldOverrideDefaultValueWithAttributes()
    {
        //when
        $product = ProductWithDefaults::create(array('description' => 'overridden desc', 'name' => 'overridden name'));

        //then
        $this->assertEquals('overridden desc', $product->description);
        $this->assertEquals('overridden name', $product->name);
    }

    /**
     * @test
     */
    public function shouldNotUseDefaultValueWhenObjectIsLoadedFromDb()
    {
        // given
        ProductWithDefaults::create(array('name' => 'Guybrush Threepwood', 'description' => 'Mighty pirate!'));

        //when
        $product = ProductWithDefaults::where()->fetch();

        //then
        $this->assertEquals('Guybrush Threepwood', $product->name);
        $this->assertEquals('Mighty pirate!', $product->description);
    }

    /**
     * @test
     */
    public function shouldEvaluateCallableDefaultsEveryTime()
    {
        // given
        $product1 = new ProductWithDefaults(array('description' => '1'));

        //when
        ProductWithDefaults::$defaultName = 'new default';
        $product2 = new ProductWithDefaults(array('description' => '2'));

        //then
        $this->assertEquals('no name', $product1->name);
        $this->assertEquals('new default', $product2->name);
    }
}
