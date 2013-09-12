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
        $this->assertContains('<option value="0">male</option>', $result);
        $this->assertContains('<option value="1">female</option>', $result);
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
            <input type="text" id="gender" name="gender" value="val" style="" />
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
        $this->assertContains('<input type="text" id="gender_"', $result);
    }

    /**
     * @test
     */
    public function shouldUseGivenIdInTextField()
    {
        // when
        $result = textField('Gender', 'gender[]', '', array('id' => 'xyz'));

        // then
        $this->assertContains('<input type="text" id="xyz"', $result);
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
        $this->assertContains('<input type="text" id="gender" name="gender" value="val" style="" custom_attribute="custom_value" ', $result);
    }

    /**
     * @test
     */
    public function shouldAddReadOnly()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('readonly' => true));

        //then
        $this->assertContains('<input type="text" id="gender" name="gender" value="val" style="" readonly="1" ', $result);
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

}