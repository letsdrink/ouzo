<?php
use Application\Model\Test\ProductWithDefaults;
use Ouzo\Tests\DbTransactionalTestCase;

class ModelDefaultFieldValuesTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldUseDefaultValueWhenCreatingNewInstance()
    {
        //when
        $product = new ProductWithDefaults();

        //then
        $this->assertEquals('no desc', $product->description);
        $this->assertEquals('no name', $product->name);
    }

    /**
     * @test
     */
    public function shouldOverrideDefaultValueWithAttributesWhenCreatingNewInstance()
    {
        //when
        $product = new ProductWithDefaults(array('description' => 'overridden desc', 'name' => 'overridden name'));

        //then
        $this->assertEquals('overridden desc', $product->description);
        $this->assertEquals('overridden name', $product->name);
    }

    /**
     * @test
     */
    public function createShouldUseDefaultValue()
    {
        //when
        $product = ProductWithDefaults::create();

        //then
        $this->assertEquals('no desc', $product->description);
        $this->assertEquals('no name', $product->name);
    }

    /**
     * @test
     */
    public function createShouldOverrideDefaultValueWithAttributes()
    {
        //when
        $product = ProductWithDefaults::create(array('description' => 'overridden desc', 'name' => 'overridden name'));

        //then
        $this->assertEquals('overridden desc', $product->description);
        $this->assertEquals('overridden name', $product->name);
    }

    /**
     * @test
     */
    public function shouldNotUseDefaultValueWhenObjectIsLoadedFromDb()
    {
        // given
        ProductWithDefaults::create(array('name' => 'Guybrush Threepwood', 'description' => 'Mighty pirate!'));

        //when
        $product = ProductWithDefaults::where()->fetch();

        //then
        $this->assertEquals('Guybrush Threepwood', $product->name);
        $this->assertEquals('Mighty pirate!', $product->description);
    }
}
