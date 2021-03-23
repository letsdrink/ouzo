<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Model;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Arrays;

class ModelRelationConditionsTest extends DbTransactionalTestCase
{
    private Category $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create(['name' => 'sony']);
        Product::create(['name' => 'bob', 'id_category' => $this->category->getId()]);
        Product::create(['name' => 'billy', 'id_category' => $this->category->getId()]);
        Product::create(['name' => 'peter', 'id_category' => $this->category->getId()]);
    }

    /**
     * @test
     */
    public function shouldLazilyFetchHasManyWithStringCondition()
    {
        //when
        $products_starting_from_b = $this->category->products_starting_with_b;

        //then
        Assert::thatArray($products_starting_from_b)->hasSize(2)->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyWithStringCondition()
    {
        //when
        $searchCategory = Category::where()->with('products_starting_with_b')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($searchCategory, 'products_starting_with_b'))
            ->hasSize(2)
            ->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldLazilyFetchHasManyWithCallbackCondition()
    {
        //when
        $products_ending_with_b_or_y = $this->category->products_ending_with_b_or_y;

        //then
        Assert::thatArray($products_ending_with_b_or_y)->hasSize(2)->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyWithCallbackCondition()
    {
        //when
        $searchCategory = Category::where()->with('products_ending_with_b_or_y')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($searchCategory, 'products_ending_with_b_or_y'))
            ->hasSize(2)
            ->onProperty('name')->containsOnly('bob', 'billy');
    }

    /**
     * @test
     */
    public function shouldLazilyFetchHasManyWithArrayCondition()
    {
        //when
        $products_name_bob = $this->category->products_name_bob;

        //then
        Assert::thatArray($products_name_bob)->hasSize(1)->onProperty('name')->containsOnly('bob');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyWithArrayCondition()
    {
        //when
        $searchCategory = Category::where()->with('products_name_bob')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($searchCategory, 'products_name_bob'))
            ->hasSize(1)
            ->onProperty('name')->containsOnly('bob');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyJoinWithStringCondition()
    {
        //given
        $category = Category::create(['name' => 'samsung']);
        Product::create(['name' => 'cris', 'id_category' => $category->getId()]);

        //when
        $searchCategory = Category::innerJoin('products_starting_with_b')->fetchAll();

        //then
        Assert::thatArray($searchCategory)->hasSize(2)->onProperty('name')->containsOnly('sony', 'sony');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyJoinWithCallbackCondition()
    {
        //given
        $category = Category::create(['name' => 'samsung']);
        Product::create(['name' => 'cris', 'id_category' => $category->getId()]);

        //when
        $searchCategory = Category::innerJoin('products_ending_with_b_or_y')->fetchAll();

        //then
        Assert::thatArray($searchCategory)->hasSize(2)->onProperty('name')->containsOnly('sony', 'sony');
    }

    /**
     * @test
     */
    public function shouldFetchHasManyJoinWithArrayCondition()
    {
        //given
        $category = Category::create(['name' => 'samsung']);
        Product::create(['name' => 'cris', 'id_category' => $category->getId()]);

        //when
        $searchCategory = Category::innerJoin('products_name_bob')->fetchAll();

        //then
        Assert::thatArray($searchCategory)->hasSize(1)->onProperty('name')->containsOnly('sony');
    }

    /**
     * @test
     */
    public function shouldFetchHasOneJoinWithStringCondition()
    {
        //when
        $searchCategory = Category::innerJoin('product_named_billy')->fetch();

        //then
        $product = self::getNoLazy($searchCategory, 'product_named_billy');
        $this->assertEquals('billy', $product->name);
    }

    /**
     * @test
     */
    public function shouldLazilyFetchHasOneWithStringCondition()
    {
        //when
        $product = $this->category->product_named_billy;

        //then
        $this->assertEquals('billy', $product->name);
    }

    /**
     * @test
     */
    public function shouldFetchHasOneWithStringCondition()
    {
        //when
        $searchCategory = Category::where()->with('product_named_billy')->fetch();

        //then
        $product = self::getNoLazy($searchCategory, 'product_named_billy');
        $this->assertEquals('billy', $product->name);
    }

    /**
     * @test
     */
    public function shouldFetchHasOneWithAlias()
    {
        //when
        $searchCategory = Category::alias('c')->innerJoin('product_named_billy', 'p')->fetch();

        //then
        $product = self::getNoLazy($searchCategory, 'product_named_billy');
        $this->assertEquals('billy', $product->name);
    }

    public static function getNoLazy(Model $model, $attribute)
    {
        return Arrays::getValue($model->attributes(), $attribute);
    }
}
