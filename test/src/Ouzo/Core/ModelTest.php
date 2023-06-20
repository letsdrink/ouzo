<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Category;
use Application\Model\Test\ModelWithoutPrimaryKey;
use Application\Model\Test\ModelWithoutSequence;
use Application\Model\Test\Order;
use Application\Model\Test\OrderProduct;
use Application\Model\Test\Product;
use Ouzo\Db\ModelDefinition;
use Ouzo\DbException;
use Ouzo\Exception\ValidationException;
use Ouzo\Model;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

class ModelTest extends DbTransactionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        ModelDefinition::resetCache();
    }

    #[Test]
    public function shouldThrowExceptionWhenTableEmptyInPrepareParameters()
    {
        $this->expectException(InvalidArgumentException::class);

        new Model(['table' => '']);
    }

    #[Test]
    public function shouldThrowExceptionWhenFieldsEmptyInPrepareParameters()
    {
        $this->expectException(InvalidArgumentException::class);

        new Model(['table' => 't_example', 'fields' => '']);
    }

    #[Test]
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

    #[Test]
    public function shouldPersistModelWithSpecifiedPrimaryKeyValue()
    {
        //given
        $product = new Product();
        $product->id = 123;
        $product->name = 'Sport';

        //when
        $id = $product->insert();

        //then
        $this->assertEquals(123, $id);

        $actual = Product::findById($id);
        $this->assertEquals('Sport', $actual->name);
    }

    #[Test]
    public function shouldFilterConstructorData()
    {
        //given
        $data = ['name' => 'Sport', 'junk' => 'Junk'];

        // when
        $product = new Product($data);

        // then
        $this->assertEquals('Sport', $product->name);
        $this->assertNull($product->junk);
    }

    #[Test]
    public function shouldFilterAttributesForInsert()
    {
        //given
        $product = new Product(['name' => 'Sport']);
        $product->junk = 'junk';

        // when
        $id = $product->insert(); //insert with junk would fail

        // then
        $actual = Product::findById($id);
        $this->assertEquals('Sport', $actual->name);
    }

    #[Test]
    public function shouldFilterAttributesForUpdate()
    {
        //given
        $product = Product::create(['name' => 'Sport']);
        $product->junk = 'junk';

        // when
        $product->update(); //update with junk would fail

        // then
        $actual = Product::findById($product->getId());
        $this->assertEquals('Sport', $actual->name);
    }

    #[Test]
    public function shouldUpdateAttributesToNull()
    {
        //given
        $product = Product::create(['name' => 'Sport', 'price' => '123']);
        $product->assignAttributes(['description' => null]);

        // when
        $product->update();

        // then
        $actual = Product::findById($product->getId());
        $this->assertNull($actual->price);
    }

    #[Test]
    public function shouldHaveNoErrorsWhenModelIsValid()
    {
        //given
        $product = new Product(['name' => 'Sport']);

        //when
        $product->isValid();

        //then
        $this->assertEmpty($product->getErrors());
    }

    #[Test]
    public function shouldHaveErrorsWhenModelIsNotValid()
    {
        //given
        $product = new Product([]);

        //when
        $product->isValid();

        //then
        $this->assertNotEmpty($product->getErrors());
        $this->assertNotEmpty($product->getErrorFields());
    }

    #[Test]
    public function shouldReturnPrimaryKey()
    {
        //given
        $product = Product::create(['name' => 'name1']);
        $id = $product->getId();

        $savedProduct = Product::findById($id);

        //when
        $savedId = $savedProduct->getId();

        //then
        $this->assertEquals($id, $savedId);
    }

    #[Test]
    public function shouldFailIfModelWithGivenIdDoesNotExists()
    {
        //given
        $invalidId = 345345345;

        //when
        CatchException::when(new Product())->findById($invalidId);
        CatchException::assertThat()->isInstanceOf(DbException::class);
    }

    #[Test]
    public function shouldSetIdInInsert()
    {
        //when
        $product = Product::create(['name' => 'name']);

        //then
        $this->assertTrue(is_numeric($product->id));
    }

    #[Test]
    public function shouldUpdateModel()
    {
        //given
        $product = Product::create(['name' => 'Tech']);
        $product->name = 'new name';

        //when
        $product->update();

        //then
        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('new name', $updatedProduct->name);
    }

    #[Test]
    public function shouldUpdateModelMultipleTimes()
    {
        //given
        $product = Product::create(['name' => 'Tech']);
        $product->name = 'new name';
        $product->update();

        $product->name = 'another name';

        //when
        $product->update();

        //then
        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('another name', $updatedProduct->name);
    }

    #[Test]
    public function shouldUpdateModelAttributes()
    {
        //given
        $product = Product::create(['name' => 'name1', 'description' => 'desc1']);

        //when
        $result = $product->updateAttributesIfValid(['name' => 'new name']);

        //then
        $this->assertTrue($result);

        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('new name', $updatedProduct->name);
        $this->assertEquals('desc1', $updatedProduct->description);
    }

    #[Test]
    public function shouldFailedOnUpdateAttributesIfModelNotValid()
    {
        //given
        $product = Product::create(['name' => 'name1', 'description' => 'desc1']);

        //when
        CatchException::when($product)->updateAttributes(['name' => null]);

        //then
        CatchException::assertThat()
            ->isInstanceOf(ValidationException::class)
            ->hasMessage("Validation failed. Empty name");
    }

    #[Test]
    public function updateShouldOnlyUpdateChangedFieldsForCreatedModel()
    {
        //given
        $product = Product::create(['name' => 'Tech', 'description' => 'Desc']);
        $product->name = 'new name';

        Product::where()->update(['description' => 'Something else']);

        //when
        $product->update();

        //then
        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('new name', $updatedProduct->name);
        $this->assertEquals('Something else', $updatedProduct->description);
    }

    #[Test]
    public function updateShouldOnlyUpdateChangedFieldsForFetchedModel()
    {
        //given
        Product::create(['name' => 'Tech', 'description' => 'Desc']);
        $product = Product::where()->fetch();

        $product->name = 'new name';

        Product::where()->update(['description' => 'Something else']);

        //when
        $product->update();

        //then
        $updatedProduct = Product::findById($product->getId());
        $this->assertEquals('new name', $updatedProduct->name);
        $this->assertEquals('Something else', $updatedProduct->description);
    }

    #[Test]
    public function shouldFilterOutNullAttributesSoThatInsertedAndLoadedObjectsAreEqual()
    {
        //given
        $product = Product::create(['name' => 'name']);

        //when
        $loadedProduct = Product::findById($product->getId());

        //then
        $this->assertEquals($product->attributes(), $loadedProduct->attributes());
    }

    #[Test]
    public function shouldBeSameReturnIn_FindById_and_FindByIdOrNull()
    {
        //given
        $product = Product::create(['name' => 'name']);

        //when
        $loadedProduct1 = Product::findById($product->getId());
        $loadedProduct2 = Product::findByIdOrNull($product->getId());

        //then
        $this->assertEquals($loadedProduct1, $loadedProduct2);
    }

    #[Test]
    public function findByIdOrNullShouldReturnNullWhenIdNotFound()
    {
        //given
        Product::create(['name' => 'name']);

        //when
        $loadedProduct = Product::findByIdOrNull(00000);

        //then
        $this->assertNull($loadedProduct);
    }

    #[Test]
    public function getShouldReturnFieldValue()
    {
        //given
        $product = new Product(['name' => 'Sport']);

        //when
        $value = $product->get('name');

        //then
        $this->assertEquals('Sport', $value);
    }

    #[Test]
    public function getShouldReturnDefaultWhenNotExistentNestedField()
    {
        //given
        $product = new Product([]);

        //when
        $value = $product->get('type->name', 'default');

        //then
        $this->assertEquals('default', $value);
    }

    #[Test]
    public function getShouldReturnDefaultForNotExistentField()
    {
        //given
        $product = new Product([]);

        //when
        $value = $product->get('name', 'default');

        //then
        $this->assertEquals('default', $value);
    }

    #[Test]
    public function getShouldReturnDefaultForNullField()
    {
        //given
        $product = new Product([]);
        $product->assignAttributes(['name' => null]);

        //when
        $value = $product->get('name', 'default');

        //then
        $this->assertEquals('default', $value);
    }

    #[Test]
    public function shouldReturnNestedValueThroughLazyRelation()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'sony', 'id_category' => $category->getId()]);

        //when
        $result = $product->get('category->name');

        //then
        $this->assertEquals('phones', $result);
    }

    #[Test]
    public function getShouldInvokeMethod()
    {
        //given
        $product = new Product([]);

        //when
        $description = $product->get('getDescription()');

        //then
        $this->assertEquals($product->getDescription(), $description);
    }

    #[Test]
    public function attributesShouldReturnAllFieldsIncludingNulls()
    {
        //given
        $product = Product::create(['name' => 'Sport'])->reload();

        //when
        $attributes = $product->attributes();

        //then
        $this->assertArrayHasKey('description', $attributes);
        $this->assertArrayHasKey('id_category', $attributes);
        $this->assertArrayHasKey('name', $attributes);
    }

    #[Test]
    public function shouldReturnDerivedClassNameInInspect()
    {
        //given
        $product = Product::create(['name' => 'Sport']);

        //when
        $string = $product->inspect();

        //then
        Assert::thatString($string)
            ->isEqualTo('Application\Model\Test\Product[<name> => "Sport", <id> => ' . $product->id . ']');
    }

    #[Test]
    public function shouldNotIncludeBlankPrimaryKeyInFields()
    {
        //given
        $model = new Model(['table' => 't_example', 'primaryKey' => '', 'fields' => ['field1']]);

        //when
        $fields = $model->_getFields();

        //then
        $this->assertEquals(['field1'], $fields);
    }

    #[Test]
    public function shouldReturnValueByMagicGetter()
    {
        //given
        $product = Product::create(['name' => 'Sport']);

        //when
        $name = $product->name;

        //then
        $this->assertEquals('Sport', $name);
    }

    #[Test]
    public function shouldReturnNullWhenFieldWasNotFound()
    {
        //given
        $product = Product::create(['name' => 'Sport']);

        //when
        $value = $product->missing_field;

        //then
        $this->assertNull($value);
    }

    #[Test]
    public function shouldGetModelName()
    {
        //given
        $product = new Product();

        //when
        $modelName = $product->getModelName();

        //then
        $this->assertEquals('Product', $modelName);
    }

    #[Test]
    public function shouldThrowExceptionIfNoRelation()
    {
        $model = new Model(['fields' => ['field1']]);

        //when
        CatchException::when($model)->getRelation('invalid');
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }

    #[Test]
    public function shouldLazyFetchHasManyRelation()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $product1 = Product::create(['name' => 'sony', 'id_category' => $category->getId()]);
        $product2 = Product::create(['name' => 'samsung', 'id_category' => $category->getId()]);

        //when
        $products = $category->products;

        //then
        Assert::thatArray($products)->containsOnly($product1, $product2);
    }

    #[Test]
    public function shouldLazyFetchHasManyRelationWithoutChildren()
    {
        //given
        $category = Category::create(['name' => 'phones']);

        //when
        $products = $category->products;

        //then
        Assert::thatArray($products)->isEmpty();
    }

    #[Test]
    public function shouldLazyFetchHasOneRelation()
    {
        //given
        $product = Product::create(['name' => 'sony']);
        $orderProduct = OrderProduct::create(['id_product' => $product->getId()]);

        //when
        $fetched = $product->orderProduct;

        //then
        $this->assertEquals($orderProduct, $fetched);
    }

    #[Test]
    public function shouldLazyFetchBelongsToRelation()
    {
        //given
        $category = Category::create(['name' => 'phones']);
        $product = Product::create(['name' => 'sony', 'id_category' => $category->getId()]);

        //when
        $fetched = $product->category;

        //then
        $this->assertEquals($category, $fetched);
    }

    #[Test]
    public function shouldJoinMultipleModels()
    {
        //given
        $category1 = Category::create(['name' => 'category1']);
        $product1 = Product::create(['name' => 'sony', 'id_category' => $category1->getId()]);
        $order1 = Order::create(['name' => 'order#1']);
        OrderProduct::create(['id_order' => $order1->getId(), 'id_product' => $product1->getId()]);

        $category2 = Category::create(['name' => 'category2']);
        $product2 = Product::create(['name' => 'sony', 'id_category' => $category2->getId()]);
        $order2 = Order::create(['name' => 'order#2']);
        OrderProduct::create(['id_order' => $order2->getId(), 'id_product' => $product2->getId()]);

        //when
        $orderProducts = OrderProduct::join('product')
            ->join('order')
            ->where(['products.id' => $product1->getId()])
            ->fetchAll();

        //then
        $this->assertCount(1, $orderProducts);
        $find = Arrays::first($orderProducts);
        $this->assertEquals('order#1', $find->order->name);
        $this->assertEquals('sony', $find->product->name);
        $this->assertEquals('category1', $find->product->category->name);
    }

    #[Test]
    public function shouldFindByNativeSql()
    {
        //given
        $category = Category::create(['name' => 'phones']);

        //when
        $found = Category::findBySql("SELECT * FROM categories");

        //then
        Assert::thatArray($found)->containsOnly($category);
    }

    #[Test]
    public function shouldAcceptParamsInFindBySql()
    {
        //given
        Category::create(['name' => 'phones1']);
        Category::create(['name' => 'phones2']);

        //when
        $found = Category::findBySql("SELECT * FROM categories where name = ?", ['phones1']);

        //then
        Assert::thatArray($found)->onProperty('name')->containsOnly('phones1');
    }

    #[Test]
    public function shouldAcceptSingleParamInFindBySql()
    {
        //given
        Category::create(['name' => 'phones1']);
        Category::create(['name' => 'phones2']);

        //when
        $found = Category::findBySql("SELECT * FROM categories where name = ?", 'phones1');

        //then
        Assert::thatArray($found)->onProperty('name')->containsOnly('phones1');
    }

    #[Test]
    public function shouldThrowValidationExceptionIfModelInvalid()
    {
        //given
        $product = new Product();

        //when
        CatchException::when($product)->create([]);

        //then
        CatchException::assertThat()->isInstanceOf(ValidationException::class);
    }

    #[Test]
    public function shouldCheckIsSetVariable()
    {
        //given
        $product = new Product();
        $product->price = '123';

        //then
        $this->assertFalse(isset($product->name));
        $this->assertTrue(isset($product->price));
    }

    #[Test]
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

    #[Test]
    public function shouldThrowExceptionIfInvalidSequence()
    {
        //given
        $model = new Model([
            'table' => 'products', 'primaryKey' => 'id', 'fields' => ['name'], 'sequence' => 'invalid_seq',
            'attributes' => [
                'name' => 'name'
            ]
        ]);

        //when
        CatchException::when($model)->insert();

        //then
        if (!$model->id) {
            CatchException::assertThat()->isInstanceOf(DbException::class);
            //drivers other than postgres return last inserted id even if invalid sequence is given
        }
    }

    #[Test]
    public function shouldHandleZeroAsPrimaryKey()
    {
        //when
        $product = new Product(['id' => 0, 'name' => 'Phone']);

        //then
        $this->assertTrue(0 === $product->id);
    }

    #[Test]
    public function findByIdShould()
    {
        //when
        CatchException::when(new ModelWithoutPrimaryKey())->findById(1);

        //then
        CatchException::assertThat()
            ->isInstanceOf(DbException::class)
            ->hasMessage('Primary key is not defined for table products');
    }

    #[Test]
    public function updateShouldUpdateOnlyChangedFieldsWhenAssignAttributesIsUsed()
    {
        //given
        $product = Product::create(['name' => 'Sport', 'price' => '123']);
        $product->assignAttributes(['description' => 'Desc']);

        // when
        $product->update();

        // then
        $actual = Product::findById($product->getId());
        $this->assertEquals('Desc', $actual->description);
    }

    #[Test]
    public function updateShouldUpdateOnlyChangedFieldsWhenObjectWasCreatedByHandAndIdWasSet()
    {
        //given
        $product = Product::create(['name' => 'Sport', 'price' => '123']);
        $id = $product->getId();

        $product = new Product(['name' => 'Water', 'price' => '123', 'id' => $id]);

        // when
        $product->update();

        // then
        $actual = Product::findById($product->getId());
        $this->assertEquals('Water', $actual->name);
    }

    #[Test]
    public function shouldCountValuesWithoutWhere()
    {
        //given
        Product::create(['name' => 'Sport', 'price' => '123']);

        //when
        $count = Product::count();

        //then
        $this->assertEquals(1, $count);
    }

    #[Test]
    public function shouldUpdateModelWithoutSequence()
    {
        //given
        $model = ModelWithoutSequence::create([
            'name' => 'name',
            'id' => 1
        ]);

        //when
        $model->updateAttributes(['name' => 'new name']);

        //then
        $this->assertEquals('new name', $model->reload()->name);
    }

    #[Test]
    #[Group('non-sqlite3')]
    public function shouldSelectForUpdate()
    {
        // given
        Product::create(['name' => 'Sport', 'price' => '123']);

        // when
        $products = Product::where()->lockForUpdate()->fetchAll();

        // then
        $this->assertCount(1, $products);
    }

    #[Test]
    public function shouldInsertRecordWithoutValues()
    {
        // when
        $order = Order::create([]);

        // then
        $this->assertNotNull($order);
        $this->assertNotNull($order->getId());
        $this->assertNull($order->name);
    }

    #[Test]
    public function shouldSerializeModel()
    {
        // given
        $product = new Product(['name' => 'Sport']);

        // when
        $serialized = $product->__serialize();

        // then
        $this->assertEquals(['name' => 'Sport'], $serialized);
    }

    #[Test]
    public function shouldUnserializeModel()
    {
        // given
        $product = new Product(['name' => 'Test']);

        // when
        $product->__unserialize(['name' => 'Sport']);

        // then
        $this->assertEquals('Sport', $product->name);
    }

    #[Test]
    public function shouldSerializeAndUnserializeModel()
    {
        // given
        $product = new Product(['name' => 'Test']);
        $serialized = serialize($product);

        // when
        $unserialized = unserialize($serialized);

        // then
        $this->assertEquals('Test', $unserialized->name);
    }

    #[Test]
    public function shouldSerializeModelToJson()
    {
        // given
        $product = new Product(['name' => 'Sport']);

        // when
        $json = $product->jsonSerialize();

        // then
        $this->assertEquals('{"name":"Sport"}', $json);
    }

    #[Test]
    public function shouldNotAssignPrimaryKeyInAssignAttributes()
    {
        //given
        $product1 = Product::create(['name' => 'Sport1']);
        $product2 = Product::create(['name' => 'Sport2']);

        $product1->assignAttributes(['id' => $product2->getId(), 'name' => 'modified']);

        // when
        $product1->update();

        // then
        $this->assertEquals('Sport2', $product2->reload()->name);
        $this->assertEquals('modified', $product1->reload()->name);
    }

    #[Test]
    #[Group('non-sqlite3')]
    public function createOrUpdateShouldInsertProductWhenNotExist()
    {
        //when
        $result = Product::createOrUpdate(['name' => 'Tech']);

        //then
        $product = Product::findById($result->getId());
        $this->assertEquals('Tech', $product->name);
    }

    #[Test]
    #[Group('non-sqlite3')]
    public function createOrUpdateShouldUpdateProductWhenOneAlreadyExists()
    {
        //given
        $product = Product::create(['name' => 'Tech']);

        //when
        Product::createOrUpdate(['id' => $product->getId(), 'name' => 'new name']);

        //then
        $product->reload();
        $this->assertEquals('new name', $product->name);
    }

    #[Test]
    #[Group('non-sqlite3')]
    public function upsertShouldReturnIdOnInsert()
    {
        //given
        $product = new Product(['name' => 'Tech']);

        //when
        $id = $product->upsert();

        //then
        $this->assertNotNull($id);
    }

    #[Test]
    #[Group('non-sqlite3')]
    public function upsertShouldReturnIdOnUpdate()
    {
        //given
        $product = Product::create(['name' => 'Tech']);
        $product->name = 'new name';

        //when
        $id = $product->upsert();

        //then
        $this->assertNotNull($id);
    }

    #[Test]
    #[Group('postgres')]
    public function shouldInsertOrDoNothing()
    {
        //given
        $category = Category::create(['name' => 'Tech']);

        //when
        $newCategory = new Category();
        $newCategory->name = 'Tech';
        $insertOrDoNothing = $newCategory->insertOrDoNothing();

        //then
        $this->assertEquals($category->getId() + 1, $insertOrDoNothing);
    }
}
