<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Category;
use Application\Model\Test\Manufacturer;
use Application\Model\Test\Order;
use Application\Model\Test\OrderProduct;
use Application\Model\Test\Product;
use Ouzo\Config;
use Ouzo\Db;
use Ouzo\Db\Any;
use Ouzo\Db\ModelQueryBuilder;
use Ouzo\Db\Relation;
use Ouzo\Db\Stats;
use Ouzo\Db\WhereClause\WhereClause;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Restrictions;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Functions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ModelQueryBuilderTest extends DbTransactionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        Config::overrideProperty('debug')->with(true);
    }

    #[Test]
    public function shouldAcceptStringInWhere()
    {
        //given
        $product = Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where('name = ?', 'tech')->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    #[Test]
    public function shouldFetchResultWhenNoParameters()
    {
        //given
        $product = Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where('name is not null')->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    #[Test]
    public function shouldAcceptArrayOfColumnValuePairsInWhere()
    {
        //given
        $product = Product::create(['name' => 'tech']);

        //when
        $loadedProduct = Product::where(['name' => 'tech'])->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    #[Test]
    public function shouldAcceptArrayOfValuesInWhere()
    {
        //given
        Product::create(['name' => 'a']);
        Product::create(['name' => 'b']);

        //when
        $loadedProducts = Product::where(['name' => ['a', 'b']])->fetchAll();

        //then
        $this->assertCount(2, $loadedProducts);
    }

    #[Test]
    public function shouldIgnoreEmptyArrayForArrayOfValuesInWhere()
    {
        //given
        Product::create(['name' => 'a']);
        Product::create(['name' => 'b']);

        //when
        $loadedProducts = Product::where(['name' => []])->fetchAll();

        //then
        $this->assertCount(0, $loadedProducts);
    }

    #[Test]
    public function shouldAcceptMultipleArraysOfValuesMixedWithSingleValuesInWhere()
    {
        //given
        $product = Product::create(['name' => 'a', 'description' => 'bob']);
        Product::create(['name' => 'b', 'description' => 'john']);
        Product::create(['name' => 'c', 'description' => 'bob']);

        //when
        $loadedProducts = Product::where(['description' => 'bob', 'name' => ['a', 'b']])->fetchAll();

        //then
        $this->assertCount(1, $loadedProducts);
        $this->assertEquals($product, $loadedProducts[0]);
    }

    #[Test]
    public function shouldAcceptRestrictionsMixedWithValuesInWhere()
    {
        //given
        $product = Product::create(['name' => 'tech', 'description' => 'desc']);

        //when
        $loadedProduct = Product::where([
            'name' => Restrictions::equalTo('tech'),
            'description' => 'desc'
        ])->fetch();

        //then
        $this->assertEquals($product, $loadedProduct);
    }

    #[Test]
    public function shouldOrderResults()
    {
        //given
        $product2 = Product::create(['name' => 'b', 'description' => 'bb']);
        $product1 = Product::create(['name' => 'a', 'description' => 'aa']);

        //when
        $loadedProducts = Product::where()->order(['name asc'])->fetchAll();

        //then
        $this->assertCount(2, $loadedProducts);
        $this->assertEquals($product1, $loadedProducts[0]);
        $this->assertEquals($product2, $loadedProducts[1]);
    }

    #[Test]
    public function shouldOrderResultsByTwoColumns()
    {
        //given
        $product1 = Product::create(['name' => 'b', 'description' => 'bb']);
        $product2 = Product::create(['name' => 'a', 'description' => 'aa']);
        $product3 = Product::create(['name' => 'a', 'description' => 'bb']);

        //when
        $loadedProducts = Product::where()->order(['name asc', 'description desc'])->fetchAll();

        //then
        $this->assertCount(3, $loadedProducts);
        $this->assertEquals($product3, $loadedProducts[0]);
        $this->assertEquals($product2, $loadedProducts[1]);
        $this->assertEquals($product1, $loadedProducts[2]);
    }

    #[Test]
    public function shouldLimitAndOffsetResults()
    {
        //given
        Product::create(['name' => 'a']);
        $product = Product::create(['name' => 'b']);
        Product::create(['name' => 'c']);

        //when
        $loadedProducts = Product::where()->offset(1)->limit(1)->order('name asc')->fetchAll();

        //then
        $this->assertCount(1, $loadedProducts);
        $this->assertEquals($product, $loadedProducts[0]);
    }

    #[Test]
    public function shouldJoinWithOtherTable()
    {
        //given
        Product::create(['name' => 'other']);
        $product = Product::create(['name' => 'phones']);

        Order::create(['name' => 'other']);
        $order = Order::create(['name' => 'a']);

        OrderProduct::create(['id_order' => $order->getId(), 'id_product' => $product->getId()]);

        //when
        $products = Product::join('orderProduct')->where('id_order IS NOT NULL')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product->getId(), $products[0]->getId());
    }

    #[Test]
    public function shouldFindWhereExists()
    {
        //given
        Product::create(['name' => 'other']);
        $product = Product::create(['name' => 'phones']);

        $order = Order::create(['name' => 'a']);

        OrderProduct::create(['id_order' => $order->getId(), 'id_product' => $product->getId()]);

        //when
        $products = Product::where(WhereClause::exists(OrderProduct::where('id_product = products.id')))->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($product->getId(), $products[0]->getId());
    }

    #[Test]
    public function shouldJoinThroughOtherRelation()
    {
        //given
        $cars = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'Reno', 'id_category' => $cars->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $orderProducts = OrderProduct::join('product->category')
            ->fetchAll();

        //then
        $fetchedProduct = self::getNoLazy($orderProducts[0], 'product');
        $this->assertEquals($product->getId(), $fetchedProduct->getId());
        $this->assertEquals($cars, self::getNoLazy($fetchedProduct, 'category'));
    }

    public static function getNoLazy(Model $model, $attribute)
    {
        return Arrays::getValue($model->attributes(), $attribute);
    }

    #[Test]
    public function shouldStoreJoinedModelsInAttributeForMultipleJoins()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'sony', 'description' => 'desc', 'id_category' => $category->getId()]);
        $orderProduct = OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $products = Product::join('category')->join('orderProduct')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
        $this->assertEquals($orderProduct, self::getNoLazy($products[0], 'orderProduct'));
    }

    #[Test]
    public function shouldStoreJoinedModelInAttribute()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'sony', 'description' => 'desc', 'id_category' => $category->getId()]);

        //when
        $products = Product::join('category')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
    }

    #[Test]
    public function shouldNotOverrideFetchedModelWhenDuplicatedJoinWithDifferentAlias()
    {
        //given
        $vans = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'Reno', 'id_category' => $vans->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $orderProduct = OrderProduct::join('product->category', ['p1', 'c'])
            ->join('product', 'p2')
            ->where([
                'p1.name' => 'Reno',
                'p2.name' => 'Reno',
            ])
            ->fetch();

        //then
        $this->assertEquals($vans, self::getNoLazy(self::getNoLazy($orderProduct, 'product'), 'category'));
    }

    #[Test]
    public function shouldNotOverrideFetchedModelWhenThereIsWithForJoinedField()
    {
        //given
        $vans = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'Reno', 'id_category' => $vans->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $orderProduct = OrderProduct::join('product->category')
            ->with('product->manufacturer')
            ->fetch();

        //then
        $this->assertEquals($vans, self::getNoLazy(self::getNoLazy($orderProduct, 'product'), 'category'));
    }

    #[Test]
    public function shouldJoinHasOneRelation()
    {
        //given
        $product = Product::create(['name' => 'sony']);
        $orderProduct = OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $fetched = Product::join('orderProduct')->fetch();

        //then
        $this->assertEquals($orderProduct, $fetched->orderProduct);
    }

    #[Test]
    public function shouldJoinInlineRelation()
    {
        //given
        $product = Product::create(['name' => 'sony']);
        $orderProduct = OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $fetched = Product::join(Relation::inline([
            'destinationField' => 'op',
            'class' => 'Test\OrderProduct',
            'foreignKey' => 'id_product',
            'localKey' => 'id'
        ]))->fetch();

        //then
        $this->assertEquals($orderProduct, self::getNoLazy($fetched, 'op'));
    }

    #[Test]
    public function shouldJoinInlineRelationWithNestedRelations()
    {
        //given
        $product = Product::create(['name' => 'sony']);
        $order = Order::create(['id_order']);
        OrderProduct::create(['id_product' => $product->getId(), 'id_order' => $order->getId()]);

        //when
        $fetched = Product::join(Relation::inline([
            'destinationField' => 'op',
            'class' => 'Test\OrderProduct',
            'foreignKey' => 'id_product',
            'localKey' => 'id'
        ]))->join('op->order')->fetch();

        //then
        $this->assertEquals($order, self::getNoLazy($fetched, 'op')->order);
    }

    #[Test]
    public function shouldNotStoreJoinedModelInAttributeIfNotFound()
    {
        //given
        Product::create(['name' => 'sony', 'description' => 'desc']);

        //when
        $products = Product::join('category')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertNull($products[0]->category);
    }

    #[Test]
    public function shouldInnerJoinWithOtherTable()
    {
        //given
        $category = Category::create(['name' => 'other']);
        Product::create(['name' => 'other', 'id_category' => $category->id]);
        Product::create(['name' => 'other']);

        //when
        $products = Product::innerJoin('category')->fetchAll();

        //then
        $this->assertCount(1, $products);
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
    }

    /**
     * @test
     * @group non-sqlite3
     */
    public function shouldRightJoinWithOtherTable()
    {
        //given
        $category = Category::create(['name' => 'category 1']);
        Category::create(['name' => 'category 2']);
        Category::create(['name' => 'category 3']);
        Category::create(['name' => 'category 4']);
        Product::create(['name' => 'prod 1', 'id_category' => $category->id]);
        Product::create(['name' => 'prod 2', 'id_category' => $category->id]);

        //when
        $products = Product::where()->rightJoin('category')->fetchAll();

        //then
        $this->assertCount(5, $products);
    }

    /**
     * @test
     * @group sqlite3
     * @throws \Ouzo\Exception\ValidationException
     * @throws Exception
     */
    public function shouldThrowExceptionRightJoinWithOtherTable()
    {
        //given
        $category = Category::create(['name' => 'category 1']);
        Category::create(['name' => 'category 2']);
        Category::create(['name' => 'category 3']);
        Category::create(['name' => 'category 4']);
        Product::create(['name' => 'prod 1', 'id_category' => $category->id]);
        Product::create(['name' => 'prod 2', 'id_category' => $category->id]);
        $products = Product::where()->rightJoin('category');

        //when
        CatchException::when($products)->fetchAll();

        //then
        CatchException::assertThat()->isInstanceOf(BadMethodCallException::class);
    }

    /**
     * @test
     * @throws \Ouzo\Exception\ValidationException
     */
    public function shouldLeftJoinWithOtherTable()
    {
        //given
        $category = Category::create(['name' => 'category 1']);
        Product::create(['name' => 'prod 1', 'id_category' => $category->id]);
        Product::create(['name' => 'prod 2', 'id_category' => $category->id]);
        Product::create(['name' => 'prod 3', 'id_category' => null]);

        //when
        $products = Product::where()->leftJoin('category')->fetchAll();

        //then
        $this->assertCount(3, $products);
    }

    #[Test]
    public function shouldCountRecords()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'aa']);
        Product::create(['name' => 'b', 'description' => 'bb']);

        //when
        $count = Product::where()->count();

        //then
        $this->assertEquals(2, $count);
    }

    #[Test]
    public function shouldReturnZeroForCountingQueryThatAlwaysReturnsNothing()
    {
        //when
        $count = Product::where(['name' => []])->count();

        //then
        $this->assertEquals(0, $count);
    }

    #[Test]
    public function shouldCountRecordsWithJoinWithOtherTable()
    {
        //given
        Product::create(['name' => 'other']);
        $product = Product::create(['name' => 'phones']);

        Order::create(['name' => 'other']);
        $order = Order::create(['name' => 'a']);

        OrderProduct::create(['id_order' => $order->getId(), 'id_product' => $product->getId()]);

        //when
        $products = Product::join('orderProduct')->where('id_order IS NOT NULL')->count();

        //then
        $this->assertEquals(1, $products);
    }

    #[Test]
    public function shouldFetchWithRelationWhenTwoObjectReferenceTheSameForeignKey()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'sony', 'id_category' => $category->getId()]);
        Product::create(['name' => 'htc', 'id_category' => $category->getId()]);

        //when
        $products = Product::where()->with('category')->fetchAll();

        //then
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
        $this->assertEquals($category, self::getNoLazy($products[1], 'category'));
    }

    #[Test]
    public function shouldFetchHasOneRelation()
    {
        //given
        $product = Product::create(['name' => 'sony']);
        $orderProduct = OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $fetched = Product::where()->with('orderProduct')->fetchAll();

        //then
        $this->assertEquals($orderProduct, self::getNoLazy($fetched[0], 'orderProduct'));
    }

    #[Test]
    public function shouldFetchBelongsToRelation()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'sony', 'id_category' => $category->getId()]);

        //when
        $products = Product::where()->with('category')->fetchAll();

        //then
        $this->assertEquals($category, self::getNoLazy($products[0], 'category'));
    }

    #[Test]
    public function shouldFetchHasManyRelation()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $product1 = Product::create(['name' => 'sony', 'id_category' => $category->getId()]);
        $product2 = Product::create(['name' => 'samsung', 'id_category' => $category->getId()]);

        //when
        $category = Category::where(['name' => 'phones'])
            ->with('products')->fetch();

        //then
        Assert::thatArray(self::getNoLazy($category, 'products'))->containsOnly($product1, $product2);
    }

    /**
     * @test
     * @throws \Ouzo\Exception\ValidationException
     * @throws Exception
     */
    public function shouldThrowExceptionIfMultipleValuesForHasOne()
    {
        //given
        $product = Product::create(['name' => 'phones']);

        OrderProduct::create(['id_product' => $product->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);

        //when
        CatchException::when((new Product())->where()->with('orderProduct'))->fetchAll();
        CatchException::assertThat()->isInstanceOf(DbException::class);
    }

    #[Test]
    public function shouldNotFetchJoinedRelationOnHasMany()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $product1 = Product::create(['name' => 'sony', 'id_category' => $category->getId()]);
        Product::create(['name' => 'samsung', 'id_category' => $category->getId()]);

        //when
        $joined = Category::join('products')->where('products.id = ?', $product1->getId())->fetch();

        //then
        $this->assertNull(Arrays::getValue($joined->attributes(), 'products'));
        $this->assertEquals($category, $joined);
    }

    #[Test]
    public function shouldNotFetchRelationJoinedThroughHasMany()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $samsung = Manufacturer::create(['name' => 'samsung']);
        $product = Product::create([
            'name' => 'sony', 'id_category' => $category->getId(), 'id_manufacturer' => $samsung->getId()
        ]);
        Product::create(['name' => 'samsung', 'id_category' => $category->getId()]);

        //when
        $joined = Category::join('products->manufacturer')->where('products.id = ?', $product->getId())->fetch();

        //then
        $this->assertNull(Arrays::getValue($joined->attributes(), 'products'));
        $this->assertEquals($category, $joined);
    }

    #[Test]
    public function shouldFetchWithRelationWhenObjectHasNoForeignKeyValue()
    {
        //given
        Product::create(['name' => 'sony']);

        //when
        $products = Product::where()->with('category')->fetchAll();

        //then
        $this->assertEquals(null, $products[0]->category);
    }

    #[Test]
    public function shouldFetchRelationThroughOtherRelation()
    {
        //given
        $cars = Category::create(['name' => 'cars']);
        $vans = Category::create(['name' => 'vans', 'id_parent' => $cars->getId()]);

        $product = Product::create(['name' => 'Reno', 'id_category' => $vans->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $orderProducts = OrderProduct::where()
            ->with('product->category->parent')
            ->fetchAll();

        //then
        $this->assertEquals($product->getId(), $orderProducts[0]->product->getId());
        $this->assertEquals($cars, $orderProducts[0]->product->category->parent);
    }

    #[Test]
    public function shouldFetchRelationThroughOneToManyRelation()
    {
        //given
        $phones = Category::create(['name' => 'phones']);
        $sony = Manufacturer::create(['name' => 'sony']);
        Product::create(['name' => 'best ever sony', 'id_category' => $phones->id, 'id_manufacturer' => $sony->id]);

        $tablets = Category::create(['name' => 'tablets']);
        $samsung = Manufacturer::create(['name' => 'samsung']);
        Product::create([
            'name' => 'best ever samsung', 'id_category' => $tablets->id, 'id_manufacturer' => $samsung->id
        ]);

        //when
        $categories = Category::where()
            ->with('products->manufacturer')
            ->fetchAll();
        Stats::reset();

        //then
        Assert::thatArray(FluentArray::from($categories)
            ->map(Functions::extract()->products)
            ->flatten()
            ->map(Functions::extract()->manufacturer->name)->toArray())->containsOnly('sony', 'samsung');

        $this->assertCount(0, Stats::$queries); //no lazily loaded relations
    }

    #[Test]
    public function shouldFetchRelationThroughNullRelation()
    {
        $order = Order::create(['name' => 'name']);
        OrderProduct::create(['id_order' => $order->getId()]);

        //when
        $orderProducts = OrderProduct::where()
            ->with('product->category')
            ->fetchAll();

        //then
        $this->assertNull($orderProducts[0]->product);
    }

    #[Test]
    public function shouldFetchOtherObjectsByArbitraryAttributes()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'a', 'description' => 'phones']);

        //when
        $products = Product::where()->with('categoryWithNameByDescription')->fetchAll();

        //then
        $this->assertEquals($category, $products[0]->categoryWithNameByDescription);
    }

    #[Test]
    public function shouldNotThrowExceptionIfNoRelationWithForeignKey()
    {
        //given
        Category::create(['name' => 'phones']);
        Product::create(['name' => 'a', 'description' => 'desc']);

        //when
        $products = Product::where()->with('categoryWithNameByDescription')->fetchAll();

        //then
        $this->assertNull($products[0]->categoryWithNameByDescription);
    }

    #[Test]
    public function shouldReturnDistinctResults()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'bob']);
        Product::create(['name' => 'b', 'description' => 'john']);
        Product::create(['name' => 'c', 'description' => 'bob']);

        //when
        $result = Product::selectDistinct('description')->order('description')->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('bob', $result[0][0]);
        $this->assertEquals('john', $result[1][0]);
    }

    /**
     * @test
     * @group postgres
     */
    public function shouldReturnDistinctOnResults()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'bob']);
        Product::create(['name' => 'b', 'description' => 'john']);
        Product::create(['name' => 'c', 'description' => 'bob']);

        //when
        /** @var Product[] $products */
        $products = Product::where()
            ->distinctOn(['description'])
            ->order('description')
            ->fetchAll();

        //then
        $this->assertCount(2, $products);
        $this->assertEquals('bob', $products[0]->description);
        $this->assertEquals('john', $products[1]->description);
    }

    #[Test]
    public function shouldReturnSpecifiedColumnAsArray()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'bob']);
        Product::create(['name' => 'b', 'description' => 'john']);
        Product::create(['name' => 'c', 'description' => 'bob']);

        //when
        $result = Product::select('name')->where(['description' => 'bob'])->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0][0]);
        $this->assertEquals('c', $result[1][0]);
    }

    #[Test]
    public function shouldReturnSpecifiedColumnAsArrayByName()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'bob']);
        Product::create(['name' => 'b', 'description' => 'john']);
        Product::create(['name' => 'c', 'description' => 'bob']);

        //when
        $result = Product::select(['name'], PDO::FETCH_BOTH)->where(['description' => 'bob'])->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0]['name']);
        $this->assertEquals('c', $result[1]['name']);
    }

    #[Test]
    public function shouldReturnSpecifiedColumnsAsArrayOfArrays()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'bob']);
        Product::create(['name' => 'b', 'description' => 'john']);
        Product::create(['name' => 'c', 'description' => 'bob']);

        //when
        $result = Product::select(['name', 'description'])->where(['description' => 'bob'])->fetchAll();

        //then
        $this->assertCount(2, $result);
        $this->assertEquals('a', $result[0][0]);
        $this->assertEquals('bob', $result[0][1]);
        $this->assertEquals('c', $result[1][0]);
        $this->assertEquals('bob', $result[1][1]);
    }

    #[Test]
    public function shouldReturnSpecifiedColumnByStringAsArrayOfArrays()
    {
        //given
        Product::create(['name' => 'a', 'description' => 'bob']);

        //when
        $result = Product::select('name')->where(['description' => 'bob'])->fetchAll();

        //then
        $this->assertCount(1, $result);
        $this->assertEquals(1, sizeof($result[0]));
        $this->assertEquals('a', $result[0][0]);
    }

    #[Test]
    public function shouldNotTryToDeleteIfEmptyInClause()
    {
        //given
        $mockDb = Mock::create(Db::class);
        $builder = new ModelQueryBuilder(new Product(), $mockDb);

        //when
        $affectedRows = $builder->where(['name' => []])->deleteAll();

        //then
        $this->assertEquals(0, $affectedRows);
        //no interaction with db
        Mock::verify($mockDb)->neverReceived();
    }

    #[Test]
    public function shouldDeleteRecord()
    {
        //given
        $product = Product::create(['name' => 'a', 'description' => 'bob']);

        //when
        $product->delete();

        //then
        $allProducts = Product::all();
        $this->assertCount(0, $allProducts);
    }

    /**
     * @group non-sqlite3
     * @test
     */
    public function shouldDeleteRecordsWithUsingClause()
    {
        //given
        $category1 = Category::create(['name' => 'cat1']);
        $category2 = Category::create(['name' => 'cat2']);
        Product::create(['name' => 'pro1', 'id_category' => $category1->getId()]);
        $product = Product::create(['name' => 'pro2', 'id_category' => $category2->getId()]);

        //when
        $deleted = Product::using('category', 'c')
            ->where(['c.name' => 'cat1'])
            ->deleteAll();

        //then
        $this->assertEquals(1, $deleted);
        Assert::thatArray(Product::all())->containsExactly($product);
    }

    #[Test]
    public function shouldThrowExceptionOnInvalidQuery()
    {
        $this->expectException(DbException::class);

        Product::select('non existing column')->where()->fetchAll();
    }

    #[Test]
    public function shouldAllowChainedWheres()
    {
        //given
        $product = Product::create(['name' => 'a', 'description' => 'bob1']);
        Product::create(['name' => 'a', 'description' => 'smith']);
        Product::create(['name' => 'c', 'description' => 'bob3']);

        //when
        $products = Product::where(['name' => 'a'])
            ->where('description like(?)', 'bob%')
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    #[Test]
    public function shouldHandleBooleanTrueInWhere()
    {
        //given
        $product = Product::create(['name' => 'a', 'sale' => true]);
        Product::create(['name' => 'b', 'sale' => false]);

        //when
        $products = Product::where(['sale' => true])->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    #[Test]
    public function shouldHandleBooleanFalseInWhere()
    {
        //given
        Product::create(['name' => 'a', 'sale' => true]);
        $product = Product::create(['name' => 'b', 'sale' => false]);

        //when
        $products = Product::where(['sale' => false])->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    #[Test]
    public function shouldAllowEmptyWheres()
    {
        //given
        $product = Product::create(['name' => 'a']);
        Product::create(['name' => 'c']);

        //when
        $products = Product::where()
            ->where(['name' => 'a'])
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    #[Test]
    public function shouldAllowEmptyParametersInWhere()
    {
        //given
        $product = Product::create(['name' => 'a']);
        Product::create(['name' => 'c']);

        //when
        $products = Product::where()
            ->where("name = 'a'")
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    #[Test]
    public function shouldAllowEmptyStringAsParameterInWhere()
    {
        //given
        $product = Product::create(['name' => 'a', 'description' => '']);
        Product::create(['name' => 'c', 'description' => 'a']);

        //when
        $products = Product::where()
            ->where("description = ''")
            ->fetchAll();

        //then
        Assert::thatArray($products)->containsOnly($product);
    }

    #[Test]
    public function shouldAcceptNullParametersInWhere()
    {
        //it will return nothing but we don't want to force users to add null checks

        //given
        Product::create(['name' => 'c']);

        //when
        $products = Product::where()
            ->where("name = ?", null)
            ->fetchAll();

        //then
        $this->assertEmpty($products);
    }

    #[Test]
    public function shouldCloneBuilder()
    {
        //given
        $product = Product::create(['name' => 'a']);
        $query = Product::where();

        //when
        $query->copy()->where(['name' => 'other'])->count();

        //then
        $this->assertEquals($product, $query->fetch());
    }

    #[Test]
    public function shouldAliasTables()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        Product::create(['name' => 'a', 'id_category' => $category->getId()]);

        //when
        $product = Product::alias('p')
            ->join('category', 'c')
            ->where("p.name = 'a' and c.name = 'phones'")
            ->fetch();

        //then
        $this->assertNotNull($product);
        $this->assertEquals($category, self::getNoLazy($product, 'category'));
        Assert::thatArray($product->attributes())->containsKeyAndValue([
            'name' => 'a',
            'id_category' => $category->getId()
        ]);
    }

    #[Test]
    public function shouldAliasTablesInNestedJoin()
    {
        //given
        $cars = Category::create(['name' => 'cars']);
        $product = Product::create(['name' => 'Reno', 'id_category' => $cars->getId()]);
        OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $orderProduct = OrderProduct::alias('op')
            ->join('product->category', ['p', 'c'])
            ->where('op.id_order is null')
            ->where([
                'p.name' => 'Reno',
                'c.name' => 'cars'
            ])
            ->fetch();

        //then
        $fetchedProduct = self::getNoLazy($orderProduct, 'product');
        $this->assertEquals($product->getId(), $fetchedProduct->getId());
        $this->assertEquals($cars, self::getNoLazy($fetchedProduct, 'category'));
    }

    #[Test]
    public function shouldDoSelfJoinWithConditions()
    {
        //given
        $vehicles = Category::create(['name' => 'vehicles']);
        $sportCars = Category::create(['name' => 'sport cars', 'id_parent' => $vehicles->id]);
        $bmw = Category::create(['name' => 'bmw', 'id_parent' => $sportCars->id]);

        //when
        $category = Category::alias('c')
            ->join('parent->parent', ['p1', 'p2'])
            ->where(['c.name' => 'bmw', 'p1.name' => 'sport cars', 'p2.name' => 'vehicles'])
            ->fetch();

        //then
        $this->assertEquals($bmw->id, $category->id);
        $parent = self::getNoLazy($category, 'parent');
        $this->assertEquals($sportCars->id, $parent->id);
        $this->assertEquals($vehicles, self::getNoLazy($parent, 'parent'));
    }

    #[Test]
    public function shouldOptimizeDuplicatedJoins()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $manufacturer = Manufacturer::create(['name' => 'sony']);
        $product = Product::create([
            'name' => 'sony', 'id_category' => $category->getId(), 'id_manufacturer' => $manufacturer->id
        ]);
        OrderProduct::create(['id_product' => $product->getId()]);

        Stats::reset();

        //when
        $orderProduct = OrderProduct::join('product->category')
            ->join('product->manufacturer')
            ->fetch();

        //then
        $this->assertEquals($product->id, $orderProduct->product->id);
        $this->assertEquals($category, $orderProduct->product->category);
        $this->assertEquals($manufacturer, $orderProduct->product->manufacturer);
        $this->assertCount(1, Stats::$queries);
    }

    #[Test]
    public function shouldOptimizeDuplicatedRelationFetches()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $manufacturer = Manufacturer::create(['name' => 'sony']);
        $product = Product::create([
            'name' => 'sony', 'id_category' => $category->getId(), 'id_manufacturer' => $manufacturer->id
        ]);
        OrderProduct::create(['id_product' => $product->getId()]);

        Stats::reset();

        //when
        $orderProduct = OrderProduct::where()
            ->with('product->category')
            ->with('product->manufacturer')
            ->fetch();

        //then
        $this->assertEquals($product->id, $orderProduct->product->id);
        $this->assertEquals($category, $orderProduct->product->category);
        $this->assertEquals($manufacturer, $orderProduct->product->manufacturer);
        $this->assertCount(4, Stats::$queries);
    }

    #[Test]
    public function shouldFetchEmptyHasManyRelationSoThatLazyLoadingDoesNotTryToLoadItAgain()
    {
        //given
        Category::create(['name' => 'sony']);

        //when
        $category = Category::where(['name' => 'sony'])
            ->with('products')
            ->fetch();

        //then
        $this->assertEquals([], self::getNoLazy($category, 'products'));
    }

    #[Test]
    public function shouldFetchEmptyHasOneRelationSoThatLazyLoadingDoesNotTryToLoadItAgain()
    {
        //given
        Product::create(['name' => 'sony']);
        $product = Product::where(['products.name' => 'sony'])
            ->with('orderProduct')
            ->fetch();
        Stats::reset();

        //when
        $orderProduct = $product->orderProduct;

        //then
        $this->assertNull($orderProduct);
        $this->assertCount(0, Stats::$queries);
    }

    #[Test]
    public function shouldSetEmptyRelationInJoinSoThatLazyLoadingDoesNotTryToLoadItAgain()
    {
        //given
        Product::create(['name' => 'sony']);

        //when
        $product = Product::where(['products.name' => 'sony'])
            ->join('category')
            ->fetch();

        //then
        Assert::thatArray($product->attributes())->containsKeyAndValue(['category' => null]);
    }

    #[Test]
    public function shouldUpdateModel()
    {
        //given
        $product = Product::create(['name' => 'bob']);

        //when
        $affectedRows = Product::where(['name' => 'bob'])->update(['name' => 'eric']);

        //then
        $this->assertEquals('eric', $product->reload()->name);
        $this->assertEquals(1, $affectedRows);
    }

    #[Test]
    public function shouldGroupByCategory()
    {
        //given
        $category1 = Category::create(['name' => 'computer']);
        $category2 = Category::create(['name' => 'phone']);
        Product::create(['name' => 'notebook', 'id_category' => $category1->getId()]);
        Product::create(['name' => 'laptop', 'id_category' => $category1->getId()]);
        Product::create(['name' => 'smartphone', 'id_category' => $category2->getId()]);

        //when
        $result = Product::select('id_category, count(*)')->groupBy('id_category')->fetchAll();

        //then
        Assert::thatArray($result)->containsOnly([$category1->getId(), 2], [$category2->getId(), 1]);
    }

    #[Test]
    public function shouldGroupByCategoryUsingArray()
    {
        //given
        $category = Category::create(['name' => 'computer']);
        Product::create(['name' => 'notebook', 'id_category' => $category->getId()]);

        //when
        $result = Product::select('id_category, count(*)')->groupBy(['id_category'])->fetchAll();

        //then
        Assert::thatArray($result)->containsOnly([$category->getId(), 1]);
    }

    #[Test]
    public function shouldThrowExceptionForGroupByWithoutSelect()
    {
        $this->expectException(DbException::class);

        Product::where()->groupBy('id_category');
    }

    #[Test]
    public function shouldSelectFunctionShouldSelectOnlySpecifiedColumns()
    {
        //given
        $category1 = Category::create(['name' => 'category for billy']);
        Product::create(['name' => 'billy', 'id_category' => $category1->getId()]);

        //when
        $names = Category::select(['categories.name'])
            ->join('product_named_billy')
            ->fetch();

        //then
        Assert::thatArray($names)->containsExactly('category for billy');
    }

    /**
     * @test
     * @group non-sqlite3
     */
    public function shouldAliasTableInUpdateQuery()
    {
        //given
        Category::create(['name' => 'old']);

        //when
        Category::alias('c')
            ->where([
                'c.name' => 'old'
            ])
            ->update([
                'name' => "new"
            ]);

        //then
        Assert::thatArray(Category::all())->onProperty('name')->containsExactly('new');
    }

    /**
     * @test
     * @group sqlite3
     */
    public function shouldThrowExceptionIfAliasInUpdateQuery()
    {
        //when
        CatchException::when(Category::alias('c')
            ->where([
                'c.name' => 'old'
            ]))
            ->update([
                'name' => "new"
            ]);

        //then
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }

    #[Test]
    public function shouldSearchAnyOf()
    {
        //given
        $category1 = Category::create(['name' => 'test1']);
        $category2 = Category::create(['name' => 'test3']);
        $category3 = Category::create(['name' => 'test with parent', 'id_parent' => $category2->getId()]);

        //when
        $categories = Category::where(Any::of(['name' => ['test1', 'test2'], 'id_parent' => $category2->getId()]))
            ->fetchAll();

        //then
        Assert::thatArray($categories)->hasSize(2)
            ->onProperty('name')->containsExactly($category1->name, $category3->name);
    }

    #[Test]
    public function shouldSearchAnyOfWithRestrictions()
    {
        //given
        $category1 = Category::create(['name' => 'test1']);
        $category2 = Category::create(['name' => 'test2']);
        $category3 = Category::create(['name' => 'other name']);
        Category::create(['name' => 'some other name']);

        //when
        $categories = Category::where(Any::of(['name' => Restrictions::like('tes%'), 'id' => $category3->getId()]))
            ->fetchAll();

        //then
        Assert::thatArray($categories)->hasSize(3)
            ->onProperty('name')->containsExactly($category1->name, $category2->name, $category3->name);
    }

    #[Test]
    public function shouldSearchAnyOfAndWhereValues()
    {
        //given
        $category = Category::create(['name' => 'shop']);
        $product1 = Product::create([
            'name' => 'notebook', 'description' => 'notebook desc', 'id_category' => $category->getId()
        ]);
        $product2 = Product::create([
            'name' => 'tablet', 'description' => 'tablet desc', 'id_category' => $category->getId()
        ]);
        Product::create(['name' => 'pc', 'description' => 'pc desc']);

        //when
        $products = Product::where(Any::of(['name' => 'tablet', 'description' => Restrictions::like('%desc')]))
            ->where(['id_category' => $category->getId()])
            ->fetchAll();

        //then
        Assert::thatArray($products)->hasSize(2)
            ->onProperty('id')->containsExactly($product1->getId(), $product2->getId());
    }

    #[Test]
    public function shouldSearchForNullValue()
    {
        //given
        $category = Category::create(['name' => 'shop']);
        $product = Product::create(['name' => 'notebook', 'id_category' => $category->getId()]);

        //when
        $search = Product::where(['description' => null])->fetch();

        //then
        Assert::thatModel($search)->isEqualTo($product);
    }

    #[Test]
    public function shouldFetchIteratorAndFetchRelationsInBatches()
    {
        //given
        Product::create(['name' => '1', 'id_category' => Category::create(['name' => 'cat1'])->getId()]);
        Product::create(['name' => '2', 'id_category' => Category::create(['name' => 'cat2'])->getId()]);
        Product::create(['name' => '3', 'id_category' => Category::create(['name' => 'cat3'])->getId()]);

        Stats::reset();

        //when
        $results = Product::where()->with('category')->fetchIterator(2);

        //then
        Assert::thatArray(iterator_to_array($results))->extracting('name')->containsExactly('1', '2', '3');
        $this->assertCount(3, Stats::$queries);
    }

    #[Test]
    public function shouldThrowExceptionWhenTryingToBindObjectAsArgument()
    {
        //given
        $product = new Product();

        //when
        CatchException::when($product)->where(['name' => new stdClass()]);

        //then
        CatchException::assertThat()->isInstanceOf(DbException::class);
    }

    #[Test]
    public function shouldDeleteEach()
    {
        //given
        $category = Category::create(['name' => 'shop1']);
        Category::create(['name' => 'shop2', 'id_parent' => $category->getId()]);
        Category::create(['name' => 'shop3', 'id_parent' => $category->getId()]);

        //when
        Category::where(['id_parent' => $category->getId()])->deleteEach();

        //then
        Assert::thatArray(Category::all())->containsExactly($category);
    }
}
