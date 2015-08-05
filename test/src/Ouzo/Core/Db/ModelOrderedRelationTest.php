<?php

use Application\Model\Test\Category;
use Application\Model\Test\Product;
use Ouzo\Tests\Assert;
use Ouzo\Tests\DbTransactionalTestCase;

class ModelOrderedRelationTest extends DbTransactionalTestCase
{
    private $_category;

    public function setUp()
    {
        parent::setUp();
        $this->_category = Category::create(array('name' => 'sony'));
        Product::create(array('name' => 'a', 'id_category' => $this->_category->getId()));
        Product::create(array('name' => 'c', 'id_category' => $this->_category->getId()));
        Product::create(array('name' => 'b', 'id_category' => $this->_category->getId()));
    }

    /**
     * @test
     */
    public function shouldOrderLazilyFetchedRelation()
    {
        //when
        $products = $this->_category->products_ordered_by_name;

        //then
        Assert::thatArray($products)->onProperty('name')->containsExactly('a', 'b', 'c');
    }

    /**
     * @test
     */
    public function shouldOrderEagerlyFetchedRelation()
    {
        //given
        $category = Category::where(array('name' => 'sony'))->fetch();

        //when
        $products = $category->products_ordered_by_name;

        //then
        Assert::thatArray($products)->onProperty('name')->containsExactly('a', 'b', 'c');
    }
}
