<?php
use Model\Test\Product;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\View;

class FormHelperTest extends DbTransactionalTestCase
{
    function setUp()
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
        $attributes = array('id' => "lab", 'name' => "lab", 'size' => "1");

        //when
        $result = selectTag($items, array(2), $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1"><option value="1" >Opt1</option><option value="2" selected>Opt1</option></select>';
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
        $this->assertEquals('<input type="text" value="name" id="product_name" name="product[name]"/>', $textField1);
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
        $result1 = $form->hiddenField('name');
        $result2 = $form->hiddenField('name', 'new_name');

        //then
        $this->assertEquals('<input type="hidden" value="name" id="product_name" name="product[name]"/>', $result1);
        $this->assertEquals('<input type="hidden" value="new_name" id="product_name" name="product[name]"/>', $result2);
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
        $this->assertEquals('<input type="password" value="name" id="product_name" name="product[name]"/>', $result);
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
        $this->assertContains('value="' . $method . '" name="_method"', $form);
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
        $this->assertInstanceOf('\Model\Test\Product', $model);
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
        $attributes = array('id' => "lab", 'name' => "lab", 'size' => "1");

        //when
        $result = selectTag($items, array(2), $attributes, 'default option');

        //then
        $expected = '<select id="lab" name="lab" size="1"><option>default option</option><option value="1" >Opt1</option><option value="2" selected>Opt2</option></select>';
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
        $expected = '<select id="product_id_category" name="product[id_category]"><option>select category</option><option value="1" selected>Cat1</option><option value="2" >Cat2</option></select>';
        $this->assertEquals($expected, $result);
    }
}