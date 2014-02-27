<?php

namespace Ouzo\Tests;

use Model\Test\Category;
use Model\Test\Product;

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
        $product = new Product(array('name' => 'abc'));
        $otherProduct = new Product(array('name' => 'abc'));
        $otherProduct->non_persistent_field = 'a';

        CatchException::when(Assert::thatModel($product))->isEqualTo($otherProduct);

        CatchException::assertThat()->isInstanceOf('PHPUnit_Framework_ExpectationFailedException');
    }

    /**
     * @test
     */
    public function shouldPassIfModelsAreEqual()
    {
        $product = new Product(array('name' => 'abc'));
        $otherProduct = new Product(array('name' => 'abc'));

        Assert::thatModel($product)->isEqualTo($otherProduct);
    }
}
 