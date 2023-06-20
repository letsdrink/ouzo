<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Product;
use Ouzo\Restrictions;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RestrictionsTest extends DbTransactionalTestCase
{
    #[Test]
    public function shouldReturnResultUsingEqualToRestriction()
    {
        //given
        $product = Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::equalTo('tech')])->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    #[Test]
    public function shouldReturnResultUsingLikeRestriction()
    {
        //given
        $product = Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::like('te%')])->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    #[Test]
    public function shouldReturnNothingUsingEqualToRestrictionWhenRestrictionDoesNotMatch()
    {
        //given
        Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::equalTo('te')])->fetch();

        //then
        $this->assertNull($loadedProduct);
    }

    #[Test]
    public function shouldReturnNothingUsingLikeRestrictionWhenRestrictionDoesNotMatch()
    {
        //given
        Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::like('te')])->fetch();

        //then
        $this->assertNull($loadedProduct);
    }

    #[Test]
    public function shouldReturnModelUsingIsNullRestriction()
    {
        //given
        Product::create(['name' => 'tech', 'description' => 'some desc']);
        $product = Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['description' => Restrictions::isNull()])->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    #[Test]
    public function shouldReturnModelUsingIsNotNullRestriction()
    {
        //given
        $product = Product::create(['name' => 'tech', 'description' => 'some desc']);
        Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['description' => Restrictions::isNotNull()])->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    #[Test]
    public function shouldReturnModelUsingIsNotInRestriction()
    {
        //given
        $product = Product::create(['name' => 'name1']);
        Product::create(['name' => 'name2']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::isNotIn(['name3', 'name2'])])->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    #[Test]
    public function shouldReturnModelUsingIsNotInRestrictionWithEmptyArray()
    {
        //given
        $product = Product::create(['name' => 'name1']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::isNotIn([])])->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    #[Test]
    public function shouldReturnModelUsingIsNotInRestrictionWithEmptyArrayAndMultipleItemsInWhereClause()
    {
        //given
        $product = Product::create(['name' => 'name1', 'description' => 'desc']);

        //when
        $loadedProduct = Product::where(['description' => 'desc', 'name' => Restrictions::isNotIn([])])->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    #[Test]
    public function shouldReturnModelUsingIsNotInRestrictionWithEmptyArrayAndMultipleItemsInWhereClauseWithSeveralWhere()
    {
        //given
        $product = Product::create(['name' => 'name1', 'description' => 'desc']);

        //when
        $loadedProduct = Product::where(['description' => 'desc'])
            ->where(['name' => Restrictions::isNotIn([])])
            ->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }

    /**
     * @group non-sqlite3
     * @test
     */
    public function shouldReturnModelUsingRegexpRestriction()
    {
        //given
        $product = Product::create(['name' => 'name1', 'description' => 'desc']);
        Product::create(['name' => 'other product', 'description' => 'desc']);

        //when
        $loadedProduct = Product::where(['name' => Restrictions::regexp('ame')])->fetch();

        //then
        Assert::thatModel($loadedProduct)->isEqualTo($product);
    }
}
