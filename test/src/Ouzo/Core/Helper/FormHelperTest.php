<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\View;

class FormHelperTest extends DbTransactionalTestCase
{
    public function setUp()
    {
        parent::setUp();
        new View('test');
    }

    /**
     * @test
     */
    public function shouldGenerateSelectTag()
    {
        //given
        $items = array(1 => 'Opt1', 2 => 'Opt1');
        $attributes = array('id' => "lab", 'size' => "1");

        //when
        $result = selectTag("lab", $items, 2, $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1"><option value="1" >Opt1</option><option value="2" selected>Opt1</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateMultipleSelectList()
    {
        //given
        $items = array(1 => 'Opt1', 2 => 'Opt2', 3 => 'Opt3', 4 => 'Opt4');
        $attributes = array('multiple' => "1", 'size' => "4");

        //when
        $result = selectTag("lab", $items, array(2, 4), $attributes);

        //then
        $expected = '<select id="lab" name="lab" multiple="1" size="4"><option value="1" >Opt1</option><option value="2" selected>Opt2</option><option value="3" >Opt3</option><option value="4" selected>Opt4</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldCreateTextFieldInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product);

        //when
        $textField1 = $form->textField('name');
        $textField2 = $form->textField('name', array('id' => 'id_new'));
        $textField3 = $form->textField('name', array('style' => 'color: red;'));

        //then
        $this->assertEquals('<input type="text" id="product_name" name="product[name]" value="name"/>', $textField1);
        $this->assertContains('id="id_new"', $textField2);
        $this->assertContains('style="color: red;"', $textField3);
    }

    /**
     * @test
     */
    public function shouldCreateTextAreaInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product);

        //when
        $textArea1 = $form->textArea('name');
        $textField2 = $form->textField('name', array('id' => 'id_new'));
        $textField3 = $form->textField('name', array('rows' => 12, 'cols' => 10, 'style' => 'color: red;'));

        //then
        $this->assertEquals('<textarea id="product_name" name="product[name]">name</textarea>', $textArea1);
        $this->assertContains('id="id_new"', $textField2);
        $this->assertContains('rows="12" cols="10" style="color: red;"', $textField3);
    }

    /**
     * @test
     */
    public function shouldCreateSelectFieldInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $categories = array(1 => 'Cat1', 2 => 'Cat2');
        $form = formFor($product);

        //when
        $selectField1 = $form->selectField('id_category', $categories);
        $selectField2 = $form->selectField('name', $categories, array('id' => 'id_new'));

        //then
        $this->assertEquals(
            '<select id="product_id_category" name="product[id_category]"><option value="1" selected>Cat1</option><option value="2" >Cat2</option></select>',
            $selectField1
        );
        $this->assertContains('id="id_new"', $selectField2);
    }

    /**
     * @test
     */
    public function shouldCreateHiddenFieldInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product);

        //when
        $html = $form->hiddenField('name');

        //then
        $this->assertEquals('<input type="hidden" id="product_name" name="product[name]" value="name"/>', $html);
    }

    /**
     * @test
     */
    public function shouldCreateLabelInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product);

        //when
        $result1 = $form->label('name');
        $result2 = $form->label('description');

        //then
        $this->assertEquals('<label for="product_name">product.name</label>', $result1);
        $this->assertEquals('<label for="product_description">Product description</label>', $result2);
    }

    /**
     * @test
     */
    public function shouldCreatePasswordFieldInFormModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));

        //when
        $result = formFor($product)->passwordField('name');

        //then
        $this->assertEquals('<input type="password" id="product_name" name="product[name]" value="name"/>', $result);
    }

    /**
     * @test
     */
    public function shouldCreateUncheckedCheckboxFieldInFormModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 0));

        //when
        $result = formFor($product)->checkboxField('id_category');

        //then
        $this->assertEquals('<input name="product[id_category]" type="hidden" value="0" /><input type="checkbox" value="1" id="product_id_category" name="product[id_category]" />', $result);
    }

    /**
     * @test
     */
    public function shouldCreateCheckedCheckboxFieldInFormModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));

        //when
        $result = formFor($product)->checkboxField('id_category');

        //then
        $this->assertEquals('<input name="product[id_category]" type="hidden" value="0" /><input type="checkbox" value="1" id="product_id_category" name="product[id_category]" checked/>', $result);
    }

    /**
     * @test
     * @dataProvider requestUnsupportedMethods
     */
    public function shouldCreateWorkAroundForUnsupportedMethods($method)
    {
        //when
        $form = formTag('/users/add', $method);

        //then
        $this->assertContains('method="POST"', $form);
        $this->assertContains('name="_method" value="' . $method . '"', $form);
    }

    /**
     * @test
     * @dataProvider requestSupportedMethods
     */
    public function shouldNoCreateWorkAroundWhenSupportedMethods($method)
    {
        //when
        $form = formTag('/users/add', $method);

        //then
        $this->assertContains('method="' . $method . '"', $form);
        $this->assertNotContains('value="' . $method . '" name="_method"', $form);
    }

    /**
     * @test
     */
    public function shouldCreateFormStartTagInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 0));
        $form = formFor($product);

        //when
        $startTag = $form->start('/sample/url', 'GET', array('class' => 'form-horizontal'));

        //then
        $this->assertEquals('<form class="form-horizontal" action="/sample/url" method="GET">', $startTag);
    }

    /**
     * @test
     */
    public function shouldCreateFormEndTagInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 0));
        $form = formFor($product);

        //when
        $endTag = $form->end();

        //then
        $this->assertEquals('</form>', $endTag);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectModelObjectDelegatedAtFormBuilder()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 0));
        $form = formFor($product);

        //when
        $model = $form->getObject();

        //then
        $this->assertInstanceOf('\Application\Model\Test\Product', $model);
    }

    public function requestUnsupportedMethods()
    {
        return array(
            array('PUT'),
            array('PATCH'),
            array('DELETE')
        );
    }

    public function requestSupportedMethods()
    {
        return array(
            array('POST'),
            array('GET')
        );
    }

    /**
     * @test
     */
    public function shouldCreateLinkTo()
    {
        //when
        $linkTo = linkTo('About', '/albums/about');

        //then
        $this->assertEquals('<a href="/albums/about" >About</a>', $linkTo);
    }

    /**
     * @test
     */
    public function shouldCreateLinkToWithAttributes()
    {
        //given
        $attributes = array('class' => 'link', 'id' => 'about');

        //when
        $linkTo = linkTo('About', '/albums/about', $attributes);

        //then
        $this->assertEquals('<a href="/albums/about" class="link" id="about">About</a>', $linkTo);
    }

    /**
     * @test
     */
    public function shouldEscapeInLinkTo()
    {
        //when
        $linkTo = linkTo('<script>alert(\'hello\')</script>About', '/albums/about');

        //then
        $this->assertEquals('<a href="/albums/about" >&lt;script&gt;alert(\'hello\')&lt;/script&gt;About</a>', $linkTo);
    }

    /**
     * @test
     */
    public function shouldSetDefaultOptionInSelectTag()
    {
        //given
        $items = array(1 => 'Opt1', 2 => 'Opt2');
        $attributes = array('id' => "lab", 'size' => "1");

        //when
        $result = selectTag("lab", $items, array(2), $attributes, 'default option');

        //then
        $expected = '<select id="lab" name="lab" size="1"><option value="" >default option</option><option value="1" >Opt1</option><option value="2" selected>Opt2</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldReturnSelectTagForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product);
        $items = array(1 => 'Cat1', 2 => 'Cat2');

        //when
        $result = $form->selectField('id_category', $items);

        //then
        $expected = '<select id="product_id_category" name="product[id_category]"><option value="1" selected>Cat1</option><option value="2" >Cat2</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldSetDefaultOptionInSelectTagForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product);
        $items = array(1 => 'Cat1', 2 => 'Cat2');

        //when
        $result = $form->selectField('id_category', $items, array(), 'select category');

        //then
        $expected = '<select id="product_id_category" name="product[id_category]"><option value="" >select category</option><option value="1" selected>Cat1</option><option value="2" >Cat2</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldReturnSelectWhenOptionValuesStartFromZero()
    {
        //given
        $items = array(0 => 'Opt1', 1 => 'Opt2');
        $attributes = array('size' => "1");

        //when
        $result = selectTag("lab", $items, array(1), $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1"><option value="0" >Opt1</option><option value="1" selected>Opt2</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateRadioButtonTag()
    {
        //when
        $result = radioButtonTag('age', 33);

        //then
        $expected = '<input type="radio" id="age" name="age" value="33"/>';
        $this->assertEquals($expected, $result);
    }
}
