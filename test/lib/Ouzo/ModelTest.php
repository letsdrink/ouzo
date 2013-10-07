<?php
use Model\Test\Product;
use Ouzo\Db\Stats;
use Ouzo\DbException;
use Ouzo\Model;
use Ouzo\Tests\DbTransactionalTestCase;

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
        $this->assertTrue(is_numeric($product->id_product));
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
    public function getShouldReturnDefaultWhenNull()
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
}