<?php
use Model\Test\Category;
use Model\Test\ModelWithoutPrimaryKey;
use Model\Test\Order;
use Model\Test\OrderProduct;
use Model\Test\Product;
use Ouzo\Db;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Arrays;

class ModelTest extends DbTransactionalTestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenTableEmptyInPrepareParameters()
    {
        new Model(array('table' => ''));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionWhenFieldsEmptInPrepareParameters()
    {
        new Model(array('table' => 't_example', 'fields' => ''));
    }

    /**
     * @test
     */
    public function shouldPersistModel()
    {
        //given
        $product = new Product();
        $product->name = 'Sport';

        //when
        $id = $product->insert();

        //then
        $this->assertNotNull($id);

        $actual = Product::findById($id);
        $this->assertEquals('Sport', $actual->name);
    }

    /**
     * @test
     */
    public function shouldFilterConstructorData()
    {
        //given
        $data = array('name' => 'Sport', 'junk' => 'Junk');

        // when
        $product = new Product($data);

        // then
        $this->assertEquals('Sport', $product->name);
        $this->assertNull($product->junk);
    }

    /**
     * @test
     */
    public function shouldFilterAttributesForInsert()
    {
        //given
        $product = new Product(array('name' => 'Sport'));
        $product->junk = 'junk';

        // when
        $id = $product->insert(); //insert with junk would fail

        // then
        $actual = Product::findById($id);
        $this->assertEquals('Sport', $actual->name);
    }

    /**
     * @test
     */
    public function shouldFilterAttributesForUpdate()
    {
        //given
        $product = Product::create(array('name' => 'Sport'));
        $product->junk = 'junk';

        // when
        $product->update(); //update with junk would fail

        // then
        $actual = Product::findById($product->getId());
        $this->assertEquals('Sport', $actual->name);
    }

    /**
     * @test
     */
    public function shouldUpdateAttributesToNull()
    {
        //given
        $product = Product::create(array('name' => 'Sport', 'price' => '123'));
        $product->assignAttributes(array('description' => null));

        // when
        $product->update();

        // then
        $actual = Product::findById($product->getId());
        $this->assertNull($actual->price);
    }

    /**
     * @test
     */
    public function shouldHaveNoErrorsWhenModelIsValid()
    {
        //given
        $product = new Product(array('name' => 'Sport'));

        //when
        $product->isValid();

        //then
        $this->assertEmpty($product->getErrors());
    }

    /**
     * @test
     */
    public function shouldHaveErrorsWhenModelIsNotValid()
    {
        //given
        $product = new Product(array());

        //when
        $product->isValid();

        //then
        $this->assertNotEmpty($product->getErrors());
        $this->assertNotEmpty($product->getErrorFields());
    }

    /**
     * @test
     */
    public function shouldReturnPrimaryKey()
    {
        //given
        $product = Product::create(array('name' => 'name1'));
        $id = $product->getId();

        $savedProduct = Product::findById($id);

        //when
        $savedId = $savedProduct->getId();

        //then
        $this->assertEquals($id, $savedId);
    }

    /**
     * @test
     */
    public function shouldFailIfModelWithGivenIdDoesNotExists()
    {
        //given
        $invalidId = 345345345;

        //when
        try {
            Product::findById($invalidId);
            $this->fail();
        } // then
        catch (DbException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldSetIdInInsert()
    {
        //when
        $product = Product::create(array('name' => 'name'));

        //then
        $this->assertTrue(is_numeric($product->id));
    }

    /**
     * @test
     */
    public function shouldUpdateModel()
    {
        //given
        $product = Product::create(array('name' => 'Tech'));
        $product->name = 'new name';

        //when
        $product->update();

        //then
        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('new name', $updatedProduct->name);
    }

    /**
     * @test
     */
    public function shouldUpdateModelAttributes()
    {
        //given
        $product = Product::create(array('name' => 'name1', 'description' => 'desc1'));

        //when
        $result = $product->updateAttributes(array('name' => 'new name'));

        //then
        $this->assertTrue($result);

        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('new name', $updatedProduct->name);
        $this->assertEquals('desc1', $updatedProduct->description);
    }

    /**
     * @test
     */
    public function shouldFilterOutNullAttributesSoThatInsertedAndLoadedObjectsAreEqual()
    {
        //given
        $product = Product::create(array('name' => 'name'));

        //when
        $loadedProduct = Product::findById($product->getId());

        //then
        $this->assertEquals($product->attributes(), $loadedProduct->attributes());
    }

    /**
     * @test
     */
    public function shouldBeSameReturnIn_FindById_and_FindByIdOrNull()
    {
        //given
        $product = Product::create(array('name' => 'name'));

        //when
        $loadedProduct1 = Product::findById($product->getId());
        $loadedProduct2 = Product::findByIdOrNull($product->getId());

        //then
        $this->assertEquals($loadedProduct1, $loadedProduct2);
    }

    /**
     * @test
     */
    public function findByIdOrNullShouldReturnNullWhenIdNotFound()
    {
        //given
        Product::create(array('name' => 'name'));

        //when
        $loadedProduct = Product::findByIdOrNull(00000);

        //then
        $this->assertNull($loadedProduct);

    }

    /**
     * @test
     */
    public function getShouldReturnFieldValue()
    {
        //given
        $product = new Product(array('name' => 'Sport'));

        //when
        $value = $product->get('name');

        //then
        $this->assertEquals('Sport', $value);
    }

    /**
     * @test
     */
    public function getShouldReturnDefaultWhenNotExistentNestedField()
    {
        //given
        $product = new Product(array());

        //when
        $value = $product->get('type->name', 'default');

        //then
        $this->assertEquals('default', $value);
    }

    /**
     * @test
     */
    public function getShouldReturnDefaultForNotExistentField()
    {
        //given
        $product = new Product(array());

        //when
        $value = $product->get('name', 'default');

        //then
        $this->assertEquals('default', $value);
    }

    /**
     * @test
     */
    public function getShouldReturnDefaultForNullField()
    {
        //given
        $product = new Product(array());
        $product->assignAttributes(array('name' => null));

        //when
        $value = $product->get('name', 'default');

        //then
        $this->assertEquals('default', $value);
    }

    /**
     * @test
     */
    public function shouldReturnNestedValueThroughLazyRelation()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'sony', 'id_category' => $category->getId()));

        //when
        $result = $product->get('category->name');

        //then
        $this->assertEquals('phones', $result);
    }

    /**
     * @test
     */
    public function getShouldInvokeMethod()
    {
        //given
        $product = new Product(array());

        //when
        $description = $product->get('getDescription()');

        //then
        $this->assertEquals($product->getDescription(), $description);
    }

    /**
     * @test
     */
    public function attributesShouldReturnAllFieldsIncludingNulls()
    {
        //given
        $product = Product::create(array('name' => 'Sport'))->reload();

        //when
        $attributes = $product->attributes();

        //then
        $this->assertArrayHasKey('description', $attributes);
        $this->assertArrayHasKey('id_category', $attributes);
        $this->assertArrayHasKey('name', $attributes);
    }

    /**
     * @test
     */
    public function shouldReturnDerivedClassNameInInspect()
    {
        //given
        $product = Product::create(array('name' => 'Sport'));

        //when
        $string = $product->inspect();

        //then
        $this->assertStringStartsWith('Model\Test\Product', $string);
    }

    /**
     * @test
     */
    public function shouldNotIncludeBlankPrimaryKeyInFields()
    {
        //given
        $model = new Model(array('table' => 't_example', 'primaryKey' => '', 'fields' => array('field1')));

        //when
        $fields = $model->_getFields();

        //then
        $this->assertEquals(array('field1'), $fields);
    }

    /**
     * @test
     */
    public function shouldReturnValueByMagicGetter()
    {
        //given
        $product = Product::create(array('name' => 'Sport'));

        //when
        $name = $product->name;

        //then
        $this->assertEquals('Sport', $name);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenFieldWasNotFound()
    {
        //given
        $product = Product::create(array('name' => 'Sport'));

        //when
        $value = $product->missing_field;

        //then
        $this->assertNull($value);
    }

    /**
     * @test
     */
    public function shouldGetModelName()
    {
        //given
        $product = new Product();

        //when
        $modelName = $product->getModelName();

        //then
        $this->assertEquals('Product', $modelName);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoRelation()
    {
        $model = new Model(array('fields' => array('field1')));

        //when
        try {
            $model->getRelation('invalid');
            $this->fail();
        } //then
        catch (InvalidArgumentException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldLazyFetchHasManyRelation()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $product1 = Product::create(array('name' => 'sony', 'id_category' => $category->getId()));
        $product2 = Product::create(array('name' => 'samsung', 'id_category' => $category->getId()));

        //when
        $products = $category->products;

        //then
        Assert::thatArray($products)->containsOnly($product1, $product2);
    }

    /**
     * @test
     */
    public function shouldLazyFetchHasManyRelationWithoutChildren()
    {
        //given
        $category = Category::create(array('name' => 'phones'));

        //when
        $products = $category->products;

        //then
        Assert::thatArray($products)->isEmpty();
    }

    /**
     * @test
     */
    public function shouldLazyFetchHasOneRelation()
    {
        //given
        $product = Product::create(array('name' => 'sony'));
        $orderProduct = OrderProduct::create(array('id_product' => $product->getId()));

        //when
        $fetched = $product->orderProduct;

        //then
        $this->assertEquals($orderProduct, $fetched);
    }

    /**
     * @test
     */
    public function shouldLazyFetchBelongsToRelation()
    {
        //given
        $category = Category::create(array('name' => 'phones'));
        $product = Product::create(array('name' => 'sony', 'id_category' => $category->getId()));

        //when
        $fetched = $product->category;

        //then
        $this->assertEquals($category, $fetched);
    }

    /**
     * @test
     */
    public function shouldJoinMultipleModels()
    {
        //given
        $category1 = Category::create(array('name' => 'phones'));
        $product1 = Product::create(array('name' => 'sony', 'id_category' => $category1->getId()));
        $order1 = Order::create(array('name' => 'order#1'));
        OrderProduct::create(array('id_order' => $order1->getId(), 'id_product' => $product1->getId()));

        $category2 = Category::create(array('name' => 'phones'));
        $product2 = Product::create(array('name' => 'sony', 'id_category' => $category2->getId()));
        $order2 = Order::create(array('name' => 'order#2'));
        OrderProduct::create(array('id_order' => $order2->getId(), 'id_product' => $product2->getId()));

        //when
        $orderProducts = OrderProduct::join('product')
            ->join('order')
            ->where(array('products.id' => $product1->getId()))
            ->fetchAll();

        //then
        $this->assertCount(1, $orderProducts);
        $find = Arrays::first($orderProducts);
        $this->assertEquals('order#1', $find->order->name);
        $this->assertEquals('sony', $find->product->name);
        $this->assertEquals('phones', $find->product->category->name);
    }

    /**
     * @test
     */
    public function shouldFindByNativeSql()
    {
        //given
        $category = Category::create(array('name' => 'phones'));

        //when
        $found = Category::findBySql("SELECT * FROM categories");

        //then
        Assert::thatArray($found)->containsOnly($category);
    }

    /**
     * @test
     */
    public function shouldThrowValidationExceptionIfModelInvalid()
    {
        //given
        $product = new Product();

        //when
        CatchException::when($product)->create(array());

        //then
        CatchException::assertThat()->isInstanceOf('Ouzo\ValidationException');
    }

    /**
     * @test
     */
    public function shouldCheckIsSetVariable()
    {
        //given
        $product = new Product();
        $product->price = '123';

        //then
        $this->assertFalse(isset($product->name));
        $this->assertTrue(isset($product->price));
    }

    /**
     * @test
     */
    public function shouldUnsetVariable()
    {
        //given
        $product = new Product();
        $product->name = 'Phone';

        //when
        unset($product->name);

        //then
        $this->assertEmpty($product->name);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfInvalidSequence()
    {
        //given
        $model = new Model(array('table' => 'products', 'primaryKey' => 'id', 'fields' => array('name'), 'sequence' => 'invalid_seq', 'attributes' => array(
            'name' => 'name'
        )));

        //when
        CatchException::when($model)->insert();

        //then
        if (!$model->id) {
            CatchException::assertThat()->isInstanceOf('\Ouzo\DbException');
            //drivers other than postgres return last inserted id even if invalid sequence is given
        }
    }

    /**
     * @test
     */
    public function shouldHandleZeroAsPrimaryKey()
    {
        //when
        $product = new Product(array('id' => 0, 'name' => 'Phone'));

        //then
        $this->assertTrue(0 === $product->id);
    }

    /**
     * @test
     */
    public function findByIdShould()
    {
        //when
        CatchException::when(new ModelWithoutPrimaryKey())->findById(1);

        //then
        CatchException::assertThat()
            ->isInstanceOf('\Ouzo\DbException')
            ->hasMessage('Primary key is not defined for table products');
    }
}