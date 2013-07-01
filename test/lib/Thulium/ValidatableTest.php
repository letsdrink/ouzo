<?php

use Thulium\Validatable;

class ValidatableChild extends Validatable
{
    function __construct($errors = array(), $errorFields = array())
    {
        $this->_errors = $errors;
        $this->_errorFields = $errorFields;
    }

    public function validate()
    {
        //do not reset errors
    }
}

class ValidatableParent extends Validatable
{
    private $child;

    function __construct($child, $errors = array(), $errorFields = array())
    {
        $this->_errors = $errors;
        $this->_errorFields = $errorFields;
        $this->child = $child;
    }

    public function validate()
    {
        $this->validateAssociated($this->child);
    }
}


class ValidatableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnParentAndChildErrors()
    {
        //given
        $child = new ValidatableChild(array('error from child'), array('errorField from child'));
        $validatable = new ValidatableParent($child, array('error from parent'), array('errorField from parent'));

        //when
        $validatable->validate();

        //then
        $this->assertEquals(array('error from parent', 'error from child'), $validatable->getErrors());
    }

    /**
     * @test
     */
    public function shouldReturnParentAndChildErrorFields()
    {
        //given
        $child = new ValidatableChild(array('error from child'), array('errorField from child'));
        $validatable = new ValidatableParent($child, array('error from parent'), array('errorField from parent'));

        //when
        $validatable->validate();

        //then
        $this->assertEquals(array('errorField from parent', 'errorField from child'), $validatable->getErrorFields());
    }

    /**
     * @test
     */
    public function shouldNotValidIfBothParentAndChildValid()
    {
        $child = new ValidatableChild();
        $validatable = new ValidatableParent($child);

        $this->assertTrue($validatable->isValid());
    }

    /**
     * @test
     */
    public function shouldNotBeValidIfChildNotValid()
    {
        $child = new ValidatableChild(array('error from child'), array('errorField from child'));
        $validatable = new ValidatableParent($child);

        $this->assertFalse($validatable->isValid());
    }

    /**
     * @test
     */
    public function shouldNotBeValidIfParentNotValid()
    {
        $child = new ValidatableChild();
        $validatable = new ValidatableParent($child, array('error from parent'), array('errorField from parent'));

        $this->assertFalse($validatable->isValid());
    }

}
