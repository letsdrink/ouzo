<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\Test;

class ModelOrderedRelationTest extends DbTransactionalTestCase
{
    private Category $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create(['name' => 'sony']);
        Product::create(['name' => 'a', 'id_category' => $this->category->getId()]);
        Product::create(['name' => 'c', 'id_category' => $this->category->getId()]);
        Product::create(['name' => 'b', 'id_category' => $this->category->getId()]);
    }

    #[Test]
    public function shouldOrderLazilyFetchedRelation()
    {
        //when
        $products = $this->category->products_ordered_by_name;

        //then
        Assert::thatArray($products)->onProperty('name')->containsExactly('a', 'b', 'c');
    }

    #[Test]
    public function shouldOrderEagerlyFetchedRelation()
    {
        //given
        $category = Category::where(['name' => 'sony'])->fetch();

        //when
        $products = $category->products_ordered_by_name;

        //then
        Assert::thatArray($products)->onProperty('name')->containsExactly('a', 'b', 'c');
    }
}
