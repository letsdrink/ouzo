<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
use Ouzo\Helper\ModelFormBuilder;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\View;

class ModelFormBuilderTest extends DbTransactionalTestCase
{
    public function setUp(): void
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
        $product = new Product([]);
        $product->validate();
        $formBuilder = new ModelFormBuilder($product);

        //when
        $html = $formBuilder->textField('name');

        //then
        $this->assertStringContainsString('class="error"', $html);
    }

    /**
     * @test
     */
    public function shouldAddErrorClassIfClassGiven()
    {
        //given
        $product = new Product([]);
        $product->validate();
        $formBuilder = new ModelFormBuilder($product);

        //when
        $html = $formBuilder->textField('name', ['class' => 'class1 class2']);

        //then
        $this->assertStringContainsString('class="class1 class2 error"', $html);
    }

    /**
     * @test
     */
    public function shouldNotAddEmptyClassAttribute()
    {
        //given
        $product = new Product(['name' => 'valid']);
        $formBuilder = new ModelFormBuilder($product);

        //when
        $html = $formBuilder->textField('name');

        //then
        $this->assertStringNotContainsString('class="', $html);
    }

    /**
     * @test
     */
    public function shouldGenerateNameForField()
    {
        //given
        $product = new Product();
        $formBuilder = new ModelFormBuilder($product);

        //when
        $name = $formBuilder->generateName('input');

        //then
        $this->assertEquals('product[input]', $name);
    }
}
