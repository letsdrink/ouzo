<?php

use Ouzo\View;

class FormHelperTest extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        new View('test');
    }

    /**
     * @test
     */
    function shouldReturnSelectFieldWithSelectedOption()
    {
        // when
        $result = selectField('Gender', 'gender', 'M', array('M' => 'male', 'F' => 'female'));

        // then
        $this->assertContains('value="M" selected', $result);
    }

    /**
     * @test
     */
    function shouldReturnSelectFieldWhenOnlyValuesAreGiven()
    {
        // when
        $result = selectField('Gender', 'gender', '', array('male', 'female'));

        // then
        $this->assertContains('<option value="0" >male</option>', $result);
        $this->assertContains('<option value="1" >female</option>', $result);
    }

    /**
     * @test
     */
    public function shouldDisplayTextField()
    {
        // when
        $result = textField('Gender', 'gender', 'val');

        // then
        $expectedHtml = <<<HTML
        <div class=" field">
            <label for="gender">Gender</label>
            <input type="text" value="val" id="gender" name="gender" style=""/>
        </div>
HTML;
        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * @test
     */
    public function shouldUseNameAsDefaultIdInTextField()
    {
        // when
        $result = textField('Gender', 'gender[]', '');

        // then
        $this->assertContains('<input type="text" value="" id="gender_"', $result);
    }

    /**
     * @test
     */
    public function shouldUseGivenIdInTextField()
    {
        // when
        $result = textField('Gender', 'gender[]', '', array('id' => 'xyz'));

        // then
        $this->assertContains('<input type="text" value="" id="xyz"', $result);
    }

    /**
     * @test
     */
    public function shouldUseClassInTextField()
    {
        // when
        $result = textField('Gender', 'gender', '', array('class' => 'xyz'));

        // then
        $this->assertContains('<div class="xyz field"', $result);
    }

    /**
     * @test
     */
    public function shouldAppendErrorClassIfErrorInTextField()
    {
        // when
        $result = textField('Gender', 'gender', '', array('class' => 'xyz', 'error' => true));

        // then
        $this->assertContains('<div class="xyz field field-with-error"', $result);
    }


    /**
     * @test
     */
    public function shouldUseGivenCustomHTMLAttributes()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('custom_attribute' => 'custom_value'));

        //then
        $this->assertContains('<input type="text" value="val" id="gender" name="gender" style="" custom_attribute="custom_value"', $result);
    }

    /**
     * @test
     */
    public function shouldAddReadOnly()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('readonly' => true));

        //then
        $this->assertContains('<input type="text" value="val" id="gender" name="gender" style="" readonly="1"', $result);
    }

    /**
     * @test
     */
    public function shouldNotAddReadOnlyIfFalse()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('readonly' => false));

        //then
        $this->assertNotContains('readonly', $result);
    }

    /**
     * @test
     */
    public function shouldNotAddReadOnlyNoReadOnlyOption()
    {
        //when
        $result = textField('Gender', 'gender', 'val');

        //then
        $this->assertNotContains('readonly', $result);
    }

    /**
     * @test
     */
    public function shouldGenerateTextArea()
    {
        //given
        //when
        $result = textArea('Label', 'label', 'value', array('cols' => 12, 'rows' => 11));

        //then
        $expected = <<<HTML
        <div class="field">
            <label for="label">Label</label>
            <textarea name="label" id="label" rows="11" cols="12" style="">value</textarea>
        </div>
HTML;
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateLabelWithAttributes()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('label_width' => 10));

        //then
        $expectedHtml = <<<HTML
        <div class=" field">
            <label for="gender" style="margin-left: px; width: 10px;">Gender</label>
            <input type="text" value="val" id="gender" name="gender" style=""/>
        </div>
HTML;
        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateHiddenField()
    {
        //when
        $result = hiddenField(array('name' => "Name", 'value' => 'val', 'id' => 'name'));

        //then
        $expected = <<<HTML
        <input type="hidden" value="val" id="name" name="Name"/>
HTML;
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateSelectField()
    {
        //when
        $result = selectField('Label', 'lab', 2, array(1 => 'Opt1', 2 => 'Opt1'));

        //then
        $expected = <<<HTML
        <div class="field">
            <label for="lab">Label</label>
            <select id="lab" name="lab" size="1"><option value="1" >Opt1</option><option value="2" selected>Opt1</option></select>
        </div>
HTML;
        $this->assertEquals($expected, $result);
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
    public function shouldGenerateMultiselectField()
    {
        //when
        $result = multiselectField('Label', 'lab', array(1, 2), array(1 => 'Opt1', 2 => 'Opt2', 3 => 'Opt3'), array('size' => 2, 'class' => 'className'));

        //then
        $expected = <<<HTML
        <div class="field">
            <label for="lab">Label</label>
            <select id="lab" name="lab[]" multiple="multiple" size="2" class="className"><option value="1" selected>Opt1</option><option value="2" selected>Opt2</option><option value="3" >Opt3</option></select>
        </div>
HTML;
        $this->assertEquals($expected, $result);
    }
}