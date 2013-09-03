<?php

use Model\Category;
use Model\Order;
use Model\OrderProduct;
use Model\Product;
use Thulium\Db\ModelQueryBuilder;
use Thulium\Db\Stats;
use Thulium\Tests\DbTransactionalTestCase;

class ModelQueryBuilderTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldAcceptStringInWhere()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where('name = ?', 'tech')->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldAcceptArrayOfColumnValuePairsInWhere()
    {
        //given
        $product = Product::create(array('name' => 'tech'));

        //when
        $loadedProduct = Product::where(array('name' => 'tech'))->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    /**
     * @test
     */
    public function shouldAcceptArrayOfValuesInWhere()
    {
        //given
        Product::create(array('name' => 'a'));
        Product::create(array('name' => 'b'));

        //when
        $loadedProducts = Product::where(array('name' => array('a', 'b')))->fetchAll();

        //then
        $this->assertCount(2, $loadedProducts);
    }

    /**
     * @test
     */
    public function shouldIgnoreEmptyArrayForArrayOfValuesInWhere()
    {
        //given
        Product::create(array('name' => 'a'));
        Product::create(array('name' => 'b'));

        //when
        $loadedProducts = Product::where(array('name' => array()))->fetchAll();

        //then
        $this->assertCount(0, $loadedProducts);
    }

    /**
     * @test
     */
    public function shouldAcceptMultipleArraysOfValuesMixedWithSingleValuesInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $loadedProducts = Product::where(array('description' => 'bob', 'name' => array('a', 'b')))->fetchAll();

        //then
        $this->assertCount(1, $loadedProducts);
        $this->assertEquals($product, $loadedProducts[0]);
    }

    /**
     * @test
     */
    public function shouldOrderResults()
    {
        //given
        $product2 = Product::create(array('name' => 'b', 'description' => 'bb'));
        $product1 = Product::create(array('name' => 'a', 'description' => 'aa'));

        //when
        $loadedProducts = Product::where()->order(array('name asc'))->fetchAll();

        //then
        $this->assertCount(2, $loadedProducts);
        $this->assertEquals($product1, $loadedProducts[0]);
        $this->assertEquals($product2, $loadedProducts[1]);
    }

    /**
     * @test
     */
    public function shouldOrderResultsByTwoColumns()
    {
        //given
        $product1 = Product::create(array('name' => 'b', 'description' => 'bb'));
        $product2 = Product::create(array('name' => 'a', 'description' => 'aa'));
        $product3 = Product::create(array('name' => 'a', 'description' => 'bb'));

        //when
        $loadedProducts = Product::where()->order(array('name asc', 'description desc'))->fetchAll();

        //then
        $this->assertCount(3, $loadedProducts);
        $this->assertEquals($product3, $loadedProducts[0]);
        $this->assertEquals($product2, $loadedProducts[1]);
        $this->assertEquals($product1, $loadedProducts[2]);
    }

    /**
     * @test
     */
    public function shouldLimitAndOffsetResults()
    {
        //given
        Product::create(array('name' => 'a'));
        $product = Product::create(array('name' => 'b'));
        Product::create(array('name' => 'c'));

        //when
        $loadedProducts = Product::where()->offset(1)->limit(1)->order('name asc')->fetchAll();

        //then
        $this->assertCount(1, $loadedProducts);
        $this->assertEquals($product, $loadedProducts[0]);
    }

    /**
     * @test
     */
    public function shouldJoinWithOtherTable()
    {
        //given
        Product::create(array('name' => 'other'));
        $product = Product::create(array('name' => 'phones'));

        Order::create(array('name' => 'other'));
        $order = Order::create(array('name' => 'a'));

        OrderProduct::create(array('id_order' => $order->getId(), 'id_product' => $product->getId()));

        //when
        $products = Product::join('OrderProduct', 'id_product')->where('id_order_products IS NOT NULL AND id_order_products <> ?', 0)->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product->getId(), $products[0]->getId());
    }

    /**
     * @test
     */
    public function shouldCountRecords()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'aa'));
        Product::create(array('name' => 'b', 'description' => 'bb'));

        //when
        $count = Product::where()->count();

        //then
        $this->assertEquals(2, $count);
    }

    /**
     * @test
     */
    public function shouldCountRecordsWithJoinWithOtherTable()
    {
        //given
        Product::create(array('name' => 'other'));
        $product = Product::create(array('name' => 'phones'));

        Order::create(array('name' => 'other'));
        $order = Order::create(array('name' => 'a'));

        OrderProduct::create(array('id_order' => $order->getId(), 'id_product' => $product->getId()));

        //when
        $products = Product::join('OrderProduct', 'id_product')->where('id_order_products IS NOT NULL AND id_order_products <> ?', 0)->count();

        //then
        $this->assertEquals(1, $products);
    }

    /**
     * @test
     */
    public function shouldFetchWithRelationWhenTwoObjectReferenceTheSameForeignKey()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'sony', 'id_category' => $category->getId()));
        Product::create(array('name' => 'htc', 'id_category' => $category->getId()));

        //when
        $products = Product::where()->with('Category', 'id_category', 'category')->fetchAll();

        //then
        $this->assertEquals($category, $products[0]->category);
        $this->assertEquals($category, $products[1]->category);
    }

    /**
     * @test
     */
    public function shouldFetchWithRelationWhenTwoObjectHasNoForeignKeyValue()
    {
        //given
        Product::create(array('name' => 'sony'));

        //when
        $products = Product::where()->with('Category', 'id_category', 'category')->fetchAll();

        //then
        $this->assertEquals(null, $products[0]->category);
    }

    /**
     * @test
     */
    public function shouldFetchRelationThroughOtherRelation()
    {
        //given
        $category = Category::create(array('name' => 'phones'));

        $product = Product::create(array('name' => 'sony', 'id_category' => $category->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));
        OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $orderProducts = OrderProduct::where()
            ->with('Product', 'id_product', 'product')
            ->with('Category', 'product->id_category', 'category')
            ->fetchAll();

        //then
        $this->assertEquals($category, $orderProducts[0]->category);
    }

    /**
     * @test
     */
    public function shouldFetchOtherObjectsByArbitraryAttributes()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'a', 'description' => 'phones'));

        //when
        $products = Product::where()->with('Category', 'description', 'category', 'name')->fetchAll();

        //then
        $this->assertEquals($category, $products[0]->category);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoRelationWithForeignKey()
    {
        //given
        Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'a', 'description' => 'desc'));

        //when
        try {
            Product::where()->with('Category', 'description', 'category', 'name')->fetchAll();
            $this->fail();
        } //then
        catch (InvalidArgumentException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldNotThrowExceptionIfNoRelationWithForeignKeyAndAllowMissingIsTrue()
    {
        //given
        Category::create(array('name' => 'phones'));
        Product::create(array('name' => 'a', 'description' => 'desc'));

        //when
        $products = Product::where()->with('Category', 'description', 'category', 'name', true)->fetchAll();

        //then
        $this->assertNull($products[0]->category);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnAsArray()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $result = Product::select('name')->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0][0]);
        $this->assertEquals('c', $result[1][0]);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnsAsArrayOfArrays()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));
        Product::create(array('name' => 'b', 'description' => 'john'));
        Product::create(array('name' => 'c', 'description' => 'bob'));

        //when
        $result = Product::select(array('name', 'description'))->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0][0]);
        $this->assertEquals('bob', $result[0][1]);
        $this->assertEquals('c', $result[1][0]);
        $this->assertEquals('bob', $result[1][1]);
    }

    /**
     * @test
     */
    public function shouldReturnSpecifiedColumnByStringAsArrayOfArrays()
    {
        //given
        Product::create(array('name' => 'a', 'description' => 'bob'));

        //when
        $result = Product::select('name')->where(array('description' => 'bob'))->fetchAll();

        //then
        $this->assertCount(1, $result);
        $this->assertEquals(1, sizeof($result[0]));
        $this->assertEquals('a', $result[0][0]);
    }

    /**
     * @test
     */
    public function shouldNotTryToDeleteIfEmptyInClause()
    {
        //given
        $mockDb = $this->getMock('\Thulium\Db', array('query'));
        $builder = new ModelQueryBuilder(new Product(), $mockDb);

        $mockDb->expects($this->never())->method('query');

        //when
        $affectedRows = $builder->where(array('name' => array()))->deleteAll();

        //then
        $this->assertEquals(0, $affectedRows);
        //no interaction with db
    }

    /**
     * @test
     */
    public function shouldDeleteRecord()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => 'bob'));

        //when
        $product->delete();

        //then
        $allProducts = Product::all();
        $this->assertCount(0, $allProducts);
    }

    /**
     * @test
     * @expectedException \Thulium\DbException
     */
    public function shouldThrowExceptionOnInvalidQuery()
    {
        Product::select('non existing column')->where()->fetchAll();
    }

    /**
     * @test
     */
    public function shouldAllowChainedWheres()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => 'bob1'));
        Product::create(array('name' => 'a', 'description' => 'smith'));
        Product::create(array('name' => 'c', 'description' => 'bob3'));

        //when
        $products = Product::where(array('name' => 'a'))
            ->where('description like(?)', 'bob%')
            ->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product, $products[0]);
    }

    /**
     * @test
     */
    public function shouldAllowEmptyWheres()
    {
        //given
        $product = Product::create(array('name' => 'a'));
        Product::create(array('name' => 'c'));

        //when
        $products = Product::where()
            ->where(array('name' => 'a'))
            ->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product, $products[0]);
    }

    /**
     * @test
     */
    public function shouldAllowEmptyParametersInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a'));
        Product::create(array('name' => 'c'));

        //when
        $products = Product::where()
            ->where("name = 'a'")
            ->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product, $products[0]);
    }

    /**
     * @test
     */
    public function shouldAllowEmptyStringAsParameterInWhere()
    {
        //given
        $product = Product::create(array('name' => 'a', 'description' => ''));
        Product::create(array('name' => 'c', 'description' => 'a'));

        //when
        $products = Product::where()
            ->where("description = ''")
            ->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product, $products[0]);
    }

    /**
     * @test
     */
    public function shouldAcceptNullParametersInWhere()
    {
        //it will return nothing but we don't want to force users to add null checks

        //given
        Product::create(array('name' => 'c'));

        //when
        $products = Product::where()
            ->where("name = ?", null)
            ->fetchAll();

        //then
        $this->assertEmpty($products);
    }

    /**
     * @test
     */
    public function shouldCloneBuilder()
    {
        //given
        $product = Product::create(array('name' => 'a'));
        $query = Product::where();

        //when
        $query->copy()->where(array('name' => 'other'))->count();

        //then
        $this->assertEquals($product, $query->fetch());
    }

}