<?php

use Model\Test\Product;
use Ouzo\Helper\ModelFormBuilder;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\View;


class ModelFormBuilderTest extends DbTransactionalTestCase
{
    function setUp()
    {
        parent::setUp();
        new View('test');
    }

    /**
     * @test
     */
    public function shouldAddErrorClass()
    {
        //given
        $product = new Product(array());
        $product->validate();
        $formBuilder = new ModelFormBuilder($product);

        //when
        $html = $formBuilder->textField('name');

        //then
        $this->assertContains('class="error"', $html);
    }

    /**
     * @test
     */
    public function shouldAddErrorClassIfClassGiven()
    {
        //given
        $product = new Product(array());
        $product->validate();
        $formBuilder = new ModelFormBuilder($product);

        //when
        $html = $formBuilder->textField('name', array('class' => 'class1 class2'));

        //then
        $this->assertContains('class="class1 class2 error"', $html);
    }

    /**
     * @test
     */
    public function shouldNotAddEmptyClassAttribute()
    {
        //given
        $product = new Product(array('name' => 'valid'));
        $formBuilder = new ModelFormBuilder($product);

        //when
        $html = $formBuilder->textField('name');

        //then
        $this->assertNotContains('class="', $html);
    }
}