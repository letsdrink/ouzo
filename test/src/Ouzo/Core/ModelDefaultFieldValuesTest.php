<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\ProductWithDefaults;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\Test;

class ModelDefaultFieldValuesTest extends DbTransactionalTestCase
{
    #[Test]
    public function shouldUseDefaultValueWhenCreatingNewInstance()
    {
        //when
        $product = new ProductWithDefaults();

        //then
        $this->assertEquals('no desc', $product->description);
        $this->assertEquals('no name', $product->name);
    }

    #[Test]
    public function shouldOverrideDefaultValueWithAttributesWhenCreatingNewInstance()
    {
        //when
        $product = new ProductWithDefaults(['description' => 'overridden desc', 'name' => 'overridden name']);

        //then
        $this->assertEquals('overridden desc', $product->description);
        $this->assertEquals('overridden name', $product->name);
    }

    #[Test]
    public function createShouldUseDefaultValue()
    {
        //when
        $product = ProductWithDefaults::create();

        //then
        $this->assertEquals('no desc', $product->description);
        $this->assertEquals('no name', $product->name);
    }

    #[Test]
    public function createShouldOverrideDefaultValueWithAttributes()
    {
        //when
        $product = ProductWithDefaults::create(['description' => 'overridden desc', 'name' => 'overridden name']);

        //then
        $this->assertEquals('overridden desc', $product->description);
        $this->assertEquals('overridden name', $product->name);
    }

    #[Test]
    public function shouldNotUseDefaultValueWhenObjectIsLoadedFromDb()
    {
        // given
        ProductWithDefaults::create(['name' => 'Guybrush Threepwood', 'description' => 'Mighty pirate!']);

        //when
        /** @var ProductWithDefaults $product */
        $product = ProductWithDefaults::where()->fetch();

        //then
        $this->assertEquals('Guybrush Threepwood', $product->name);
        $this->assertEquals('Mighty pirate!', $product->description);
    }

    #[Test]
    public function shouldEvaluateCallableDefaultsEveryTime()
    {
        // given
        $product1 = new ProductWithDefaults(['description' => '1']);

        //when
        ProductWithDefaults::$defaultName = 'new default';
        $product2 = new ProductWithDefaults(['description' => '2']);

        //then
        $this->assertEquals('no name', $product1->name);
        $this->assertEquals('new default', $product2->name);
    }

    #[Test]
    public function shouldEvaluateDefaultsEveryTime()
    {
        // given
        $product1 = new ProductWithDefaults([]);

        //when
        ProductWithDefaults::$defaultDescription = 'desc';
        $product2 = new ProductWithDefaults([]);

        //then
        $this->assertEquals('no desc', $product1->description);
        $this->assertEquals('desc', $product2->description);
    }
}
