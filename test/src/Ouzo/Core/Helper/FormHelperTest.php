<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
use Ouzo\Csrf\CsrfProtector;
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
        $items = [1 => 'Opt1', 2 => 'Opt1'];
        $attributes = ['id' => "lab", 'size' => "1"];

        //when
        $result = selectTag("lab", $items, 2, $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1"><option value="1" >Opt1</option><option value="2" selected>Opt1</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateReadOnlySelectTag()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt1'];
        $attributes = ['id' => "lab", 'size' => "1", 'readonly' => 'readonly'];

        //when
        $result = selectTag("lab", $items, 2, $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1" readonly="readonly"><option value="1" disabled>Opt1</option><option value="2" selected>Opt1</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateMultipleSelectList()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt2', 3 => 'Opt3', 4 => 'Opt4'];
        $attributes = ['multiple' => "1", 'size' => "4"];

        //when
        $result = selectTag("lab", $items, [2, 4], $attributes);

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
        $product = new Product(['description' => 'desc', 'name' => 'name "<&', 'id_category' => 1]);
        $form = formFor($product);

        //when
        $textField1 = $form->textField('name');
        $textField2 = $form->textField('name', ['id' => 'id_new']);
        $textField3 = $form->textField('name', ['style' => 'color: red;']);

        //then
        $this->assertEquals('<input type="text" id="product_name" name="product[name]" value="name &quot;&lt;&amp;"/>', $textField1);
        $this->assertContains('id="id_new"', $textField2);
        $this->assertContains('style="color: red;"', $textField3);
    }

    /**
     * @test
     */
    public function shouldCreateTextAreaInFormForModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $form = formFor($product);

        //when
        $textArea1 = $form->textArea('name');
        $textField2 = $form->textField('name', ['id' => 'id_new']);
        $textField3 = $form->textField('name', ['rows' => 12, 'cols' => 10, 'style' => 'color: red;']);

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
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $categories = [1 => 'Cat1', 2 => 'Cat2'];
        $form = formFor($product);

        //when
        $selectField1 = $form->selectField('id_category', $categories);
        $selectField2 = $form->selectField('name', $categories, ['id' => 'id_new']);

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
        $product = new Product(['description' => 'desc', 'name' => 'name "<&', 'id_category' => 1]);
        $form = formFor($product);

        //when
        $html = $form->hiddenField('name');

        //then
        $this->assertEquals('<input type="hidden" id="product_name" name="product[name]" value="name &quot;&lt;&amp;"/>', $html);
    }

    /**
     * @test
     */
    public function shouldCreateLabelInFormForModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $form = formFor($product);

        //when
        $result1 = $form->label('name');
        $result2 = $form->label('description');
        $result3 = $form->label('id_category');

        //then
        $this->assertEquals('<label for="product_name">product.name</label>', $result1);
        $this->assertEquals('<label for="product_description">Product description</label>', $result2);
        $this->assertEquals('<label for="product_id_category">Category&gt;&quot;ID&quot;</label>', $result3);
    }

    /**
     * @test
     */
    public function shouldCreatePasswordFieldInFormModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name "<&', 'id_category' => 1]);

        //when
        $result = formFor($product)->passwordField('name');

        //then
        $this->assertEquals('<input type="password" id="product_name" name="product[name]" value="name &quot;&lt;&amp;"/>', $result);
    }

    /**
     * @test
     */
    public function shouldCreateUncheckedCheckboxFieldInFormModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 0]);

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
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);

        //when
        $result = formFor($product)->checkboxField('id_category');

        //then
        $this->assertEquals('<input name="product[id_category]" type="hidden" value="0" /><input type="checkbox" value="1" id="product_id_category" name="product[id_category]" checked/>', $result);
    }

    /**
     * @test
     * @dataProvider requestUnsupportedMethods
     * @param string $method
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
     * @param string $method
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
    public function shouldCreateFormStartTagWithCsrfTokenInFormForModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 0]);
        $form = formFor($product);

        //when
        $startTag = $form->start('/sample/url', 'GET', ['class' => 'form-horizontal']);

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<form class="form-horizontal" action="/sample/url" method="GET"><input type="hidden" id="csrftoken" name="csrftoken" value="' . CsrfProtector::getCsrfToken() . '"/>', $startTag);
    }

    /**
     * @test
     */
    public function shouldCreateFormEndTagInFormForModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 0]);
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
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 0]);
        $form = formFor($product);

        //when
        $model = $form->getObject();

        //then
        $this->assertInstanceOf('\Application\Model\Test\Product', $model);
    }

    public function requestUnsupportedMethods()
    {
        return [
            ['PUT'],
            ['PATCH'],
            ['DELETE']
        ];
    }

    public function requestSupportedMethods()
    {
        return [
            ['POST'],
            ['GET']
        ];
    }

    /**
     * @test
     */
    public function shouldCreateLinkTo()
    {
        //when
        $linkTo = linkTo('About', '/albums/about');

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<a href="/albums/about" >About</a>', $linkTo);
    }

    /**
     * @test
     */
    public function shouldCreateLinkToWithAttributes()
    {
        //given
        $attributes = ['class' => 'link', 'id' => 'about'];

        //when
        $linkTo = linkTo('About', '/albums/about', $attributes);

        //then
        /** @noinspection HtmlUnknownTarget */
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
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<a href="/albums/about" >&lt;script&gt;alert(\'hello\')&lt;/script&gt;About</a>', $linkTo);
    }

    /**
     * @test
     */
    public function shouldSetDefaultOptionInSelectTag()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt2'];
        $attributes = ['id' => "lab", 'size' => "1"];

        //when
        $result = selectTag("lab", $items, [2], $attributes, 'default option');

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
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $form = formFor($product);
        $items = [1 => 'Cat1', 2 => 'Cat2'];

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
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $form = formFor($product);
        $items = [1 => 'Cat1', 2 => 'Cat2'];

        //when
        $result = $form->selectField('id_category', $items, [], 'select category');

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
        $items = [0 => 'Opt1', 1 => 'Opt2'];
        $attributes = ['size' => "1"];

        //when
        $result = selectTag("lab", $items, [1], $attributes);

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
