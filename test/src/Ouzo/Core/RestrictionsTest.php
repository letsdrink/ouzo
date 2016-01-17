<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
use Ouzo\Restrictions;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;

class RestrictionsTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldReturnResultUsingEqualToRestriction()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::equalTo('tech')))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnResultUsingLikeRestriction()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::like('te%')))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnNothingUsingEqualToRestrictionWhenRestrictionDoesNotMatch()
    {
        //given
        Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::equalTo('te')))->fetch();

        //then
        $this->assertNull($loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnNothingUsingLikeRestrictionWhenRestrictionDoesNotMatch()
    {
        //given
        Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::like('te')))->fetch();

        //then
        $this->assertNull($loadedProduct);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingIsNullRestriction()
    {
        //given
        Product::create(array('name' => 'tech', 'description' => 'some desc'));
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('description' => Restrictions::isNull()))->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingIsNotNullRestriction()
    {
        //given
        $product = Product::create(array('name' => 'tech', 'description' => 'some desc'));
        Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('description' => Restrictions::isNotNull()))->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingIsNotInRestriction()
    {
        //given
        $product = Product::create(array('name' => 'name1'));
        Product::create(array('name' => 'name2'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::isNotIn(array('name3', 'name2'))))->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingIsNotInRestrictionWithEmptyArray()
    {
        //given
        $product = Product::create(array('name' => 'name1'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::isNotIn(array())))->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingIsNotInRestrictionWithEmptyArrayAndMultipleItemsInWhereClause()
    {
        //given
        $product = Product::create(array('name' => 'name1', 'description' => 'desc'));

        //when
        $loadedProduct = Product::where(array('description' => 'desc', 'name' => Restrictions::isNotIn(array())))->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingIsNotInRestrictionWithEmptyArrayAndMultipleItemsInWhereClauseWithSeveralWhere()
    {
        //given
        $product = Product::create(array('name' => 'name1', 'description' => 'desc'));

        //when
        $loadedProduct = Product::where(array('description' => 'desc'))
            ->where(array('name' => Restrictions::isNotIn(array())))
            ->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @test
     */
    public function shouldReturnModelUsingRegexpRestriction()
    {
        //given
        $product = Product::create(array('name' => 'name1', 'description' => 'desc'));
        Product::create(array('name' => 'other product', 'description' => 'desc'));

        //when
        $loadedProduct = Product::where(array('name' => Restrictions::regexp('ame')))->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }
}
