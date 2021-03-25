<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\ExpectationFailedException;

class ModelAssertTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldFailForModelsOfDifferentType()
    {
        $product = new Product(['name' => 'abc']);
        $category = new Category(['name' => 'abc']);

        CatchException::when(Assert::thatModel($product))->hasSameAttributesAs($category);

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    /**
     * @test
     */
    public function shouldFailIfModelsHaveDifferentAttributes()
    {
        $product = new Product(['name' => 'abc']);
        $otherProduct = new Product(['name' => 'other']);

        CatchException::when(Assert::thatModel($product))->hasSameAttributesAs($otherProduct);

        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    /**
     * @test
     */
    public function shouldNotCompareRelations()
    {
        $cars = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'Reno', 'id_category' => $cars->getId()]);
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
        $product = new Product(['name' => 'abc']);
        $otherProduct = new Product(['name' => 'abc']);
        $otherProduct->non_persistent_field = 'a';

        //when
        CatchException::when(Assert::thatModel($product))->isEqualTo($otherProduct);

        //then
        CatchException::assertThat()->isInstanceOf(ExpectationFailedException::class);
    }

    /**
     * @test
     */
    public function shouldPassIfModelsAreEqual()
    {
        //given
        $product = new Product(['name' => 'abc']);

        //when
        $otherProduct = new Product(['name' => 'abc']);

        //then
        Assert::thatModel($product)->isEqualTo($otherProduct);
    }
}
