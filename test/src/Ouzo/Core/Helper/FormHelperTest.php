<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
use Ouzo\Csrf\CsrfProtector;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\View;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class FormHelperTest extends DbTransactionalTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];
        new View('test');
    }

    #[Test]
    public function shouldGenerateSelectTag()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt1'];
        $attributes = ['id' => "lab", 'size' => "1"];

        //when
        $result = selectTag("lab", $items, 2, $attributes);

        //then
        $expected = "<select id=\"lab\" name=\"lab\" size=\"1\">\n<option value=\"1\">Opt1</option>\n<option value=\"2\" selected>Opt1</option>\n</select>";
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldGenerateReadOnlySelectTag()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt1'];
        $attributes = ['id' => "lab", 'size' => "1", 'readonly' => 'readonly'];

        //when
        $result = selectTag("lab", $items, 2, $attributes);

        //then
        $expected = "<select id=\"lab\" name=\"lab\" readonly=\"readonly\" size=\"1\">\n<option value=\"1\" disabled>Opt1</option>\n<option value=\"2\" selected>Opt1</option>\n</select>";
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldGenerateMultipleSelectList()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt2', 3 => 'Opt3', 4 => 'Opt4'];
        $attributes = ['multiple' => "1", 'size' => "4"];

        //when
        $result = selectTag("lab", $items, [2, 4], $attributes);

        //then
        $expected = "<select id=\"lab\" name=\"lab\" multiple=\"1\" size=\"4\">
<option value=\"1\">Opt1</option>
<option value=\"2\" selected>Opt2</option>
<option value=\"3\">Opt3</option>
<option value=\"4\" selected>Opt4</option>
</select>";
        $this->assertEquals($expected, $result);
    }

    #[Test]
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
        $this->assertStringContainsString('id="id_new"', $textField2);
        $this->assertStringContainsString('style="color: red;"', $textField3);
    }

    #[Test]
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
        $this->assertStringContainsString('id="id_new"', $textField2);
        $this->assertStringContainsString('rows="12"', $textField3);
        $this->assertStringContainsString('cols="10"', $textField3);
        $this->assertStringContainsString('style="color: red;"', $textField3);
    }

    #[Test]
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
            '<select id="product_id_category" name="product[id_category]">
<option value="1" selected>Cat1</option>
<option value="2">Cat2</option>
</select>',
            $selectField1
        );
        $this->assertStringContainsString('id="id_new"', $selectField2);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldCreatePasswordFieldInFormModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name "<&', 'id_category' => 1]);

        //when
        $result = formFor($product)->passwordField('name');

        //then
        $this->assertEquals('<input type="password" id="product_name" name="product[name]" value="name &quot;&lt;&amp;"/>', $result);
    }

    #[Test]
    public function shouldCreateUncheckedCheckboxFieldInFormModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 0]);

        //when
        $result = formFor($product)->checkboxField('id_category');

        //then
        $this->assertEquals('<input type="hidden" name="product[id_category]" value="0"/><input type="checkbox" id="product_id_category" name="product[id_category]" value="1"/>', $result);
    }

    #[Test]
    public function shouldCreateCheckedCheckboxFieldInFormModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);

        //when
        $result = formFor($product)->checkboxField('id_category');

        //then
        $this->assertEquals('<input type="hidden" name="product[id_category]" value="0"/><input type="checkbox" id="product_id_category" name="product[id_category]" value="1" checked/>', $result);
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
        $this->assertStringContainsString('method="POST"', $form);
        $this->assertStringContainsString('name="_method" value="' . $method . '"', $form);
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
        $this->assertStringContainsString('method="' . $method . '"', $form);
        $this->assertStringNotContainsString('value="' . $method . '" name="_method"', $form);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldReturnCorrectModelObjectDelegatedAtFormBuilder()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 0]);
        $form = formFor($product);

        //when
        $model = $form->getObject();

        //then
        $this->assertInstanceOf(Product::class, $model);
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

    #[Test]
    public function shouldCreateLinkTo()
    {
        //when
        $linkTo = linkTo('About', '/albums/about');

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<a href="/albums/about">About</a>', $linkTo);
    }

    #[Test]
    public function shouldCreateLinkToWithAttributes()
    {
        //given
        $attributes = ['class' => 'link', 'id' => 'about'];

        //when
        $linkTo = linkTo('About', '/albums/about', $attributes);

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<a id="about" class="link" href="/albums/about">About</a>', $linkTo);
    }

    #[Test]
    public function shouldEscapeInLinkTo()
    {
        //when
        $linkTo = linkTo('<script>alert(\'hello\')</script>About', '/albums/about');

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<a href="/albums/about">&lt;script&gt;alert(\'hello\')&lt;/script&gt;About</a>', $linkTo);
    }

    #[Test]
    public function shouldSetDefaultOptionInSelectTag()
    {
        //given
        $items = [1 => 'Opt1', 2 => 'Opt2'];
        $attributes = ['id' => "lab", 'size' => "1"];

        //when
        $result = selectTag("lab", $items, [2], $attributes, 'default option');

        //then
        $expected = '<select id="lab" name="lab" size="1">
<option value="">default option</option>
<option value="1">Opt1</option>
<option value="2" selected>Opt2</option>
</select>';
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldReturnSelectTagForModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $form = formFor($product);
        $items = [1 => 'Cat1', 2 => 'Cat2'];

        //when
        $result = $form->selectField('id_category', $items);

        //then
        $expected = '<select id="product_id_category" name="product[id_category]">
<option value="1" selected>Cat1</option>
<option value="2">Cat2</option>
</select>';
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldSetDefaultOptionInSelectTagForModelClass()
    {
        //given
        $product = new Product(['description' => 'desc', 'name' => 'name', 'id_category' => 1]);
        $form = formFor($product);
        $items = [1 => 'Cat1', 2 => 'Cat2'];

        //when
        $result = $form->selectField('id_category', $items, [], 'select category');

        //then
        $expected = '<select id="product_id_category" name="product[id_category]">
<option value="">select category</option>
<option value="1" selected>Cat1</option>
<option value="2">Cat2</option>
</select>';
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldReturnSelectWhenOptionValuesStartFromZero()
    {
        //given
        $items = [0 => 'Opt1', 1 => 'Opt2'];
        $attributes = ['size' => "1"];

        //when
        $result = selectTag("lab", $items, [1], $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1">
<option value="0">Opt1</option>
<option value="1" selected>Opt2</option>
</select>';
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function shouldGenerateRadioButtonTag()
    {
        //when
        $result = radioButtonTag('age', 33);

        //then
        $expected = '<input type="radio" id="age" name="age" value="33"/>';
        $this->assertEquals($expected, $result);
    }
}
