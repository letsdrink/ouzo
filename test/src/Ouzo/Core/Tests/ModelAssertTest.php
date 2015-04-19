<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;

class ModelAssertTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldFailForModelsOfDifferentType()
    {
        $product = new Product(array('name' => 'abc'));
        $category = new Category(array('name' => 'abc'));

        CatchException::when(Assert::thatModel($product))->hasSameAttributesAs($category);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldFailIfModelsHaveDifferentAttributes()
    {
        $product = new Product(array('name' => 'abc'));
        $otherProduct = new Product(array('name' => 'other'));

        CatchException::when(Assert::thatModel($product))->hasSameAttributesAs($otherProduct);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldNotCompareRelations()
    {
        $cars = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'Reno', 'id_category' => $cars->getId()));
        $productWithoutLoadedCategory = Product::findById($product->getId());

        // when relation is loaded
        $product->category;

        //then
        Assert::thatModel($product)->hasSameAttributesAs($productWithoutLoadedCategory);
    }

    /**
     * @test
     */
    public function shouldFailIfModelsAreNotEqual()
    {
        //given
        $product = new Product(array('name' => 'abc'));
        $otherProduct = new Product(array('name' => 'abc'));
        $otherProduct->non_persistent_field = 'a';

        //when
        CatchException::when(Assert::thatModel($product))->isEqualTo($otherProduct);

        //then
        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldPassIfModelsAreEqual()
    {
        //given
        $product = new Product(array('name' => 'abc'));

        //when
        $otherProduct = new Product(array('name' => 'abc'));

        //then
        Assert::thatModel($product)->isEqualTo($otherProduct);
    }
}
