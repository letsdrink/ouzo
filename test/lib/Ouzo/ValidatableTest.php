<?php

use Ouzo\Tests\Assert;
use Ouzo\Validatable;

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

    /**
     * @test
     */
    public function shouldValidateWrongDateTime()
    {
        // given
        $validatable = new Validatable();

        // when
        $validatable->validateDateTime('wrong-date-format', 'Wrong date.');

        // then
        $errors = $validatable->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Wrong date.', $errors[0]);
    }

    /**
     * @test
     */
    public function shouldValidateCorrectDateTime()
    {
        // given
        $validatable = new Validatable();

        // when
        $validatable->validateDateTime('2011-02-05', 'Wrong date.');

        // then
        $this->assertEmpty($validatable->getErrors());
    }

    /**
     * @test
     */
    public function shouldClearErrorBeforeValidation()
    {
        // given
        $validatable = new Validatable();
        $validatable->validateAssociated(new ValidatableChild(array('error'), array('errorField')));

        // when
        $validatable->validate();

        // then
        $this->assertEmpty($validatable->getErrors());
        $this->assertEmpty($validatable->getErrorFields());
    }

    /**
     * @test
     */
    public function shouldValidStringMaxLength()
    {
        //given
        $validatable = new Validatable();

        //when
        $validatable->validateStringMaxLength('string suits', 30, 'Too long string');

        //then
        $errors = $validatable->getErrors();
        $this->assertCount(0, $errors);
    }

    /**
     * @test
     */
    public function shouldNotBeValidStringMaxLength()
    {
        //given
        $validatable = new Validatable();

        //when
        $validatable->validateStringMaxLength('string is too long', 3, 'Too long string');

        //then
        $errors = $validatable->getErrors();
        $this->assertEquals('Too long string', $errors[0]);
    }

    /**
     * @test
     */
    public function shouldAddErrorIfValueIsNotTrue()
    {
        //given
        $validatable = new Validatable();

        //when
        $validatable->validateTrue(false, 'error', 'field');

        //then
        Assert::thatArray($validatable->getErrors())->containsOnly('error');
        Assert::thatArray($validatable->getErrorFields())->containsOnly('field');
    }

    /**
     * @test
     */
    public function shouldAddErrorIfValueIsBlank()
    {
        //given
        $validatable = new Validatable();

        //when
        $validatable->validateNotBlank('', 'blank', 'field');

        //then
        Assert::thatArray($validatable->getErrors())->containsOnly('blank');
        Assert::thatArray($validatable->getErrorFields())->containsOnly('field');
    }

    /**
     * @test
     */
    public function shouldNotTreatZeroAsBlank()
    {
        //given
        $validatable = new Validatable();

        //when
        $validatable->validateNotBlank('0', 'blank');

        //then
        Assert::thatArray($validatable->getErrors())->isEmpty();
        Assert::thatArray($validatable->getErrorFields())->isEmpty();
    }

    /**
     * @test
     */
    public function shouldValidateAssociatedCollection()
    {
        // given
        $validatable = new Validatable();
        $others = array(
            new ValidatableChild(array('error1'), array('errorField1')),
            new ValidatableChild(array('error2'), array('errorField2'))
        );

        // when
        $validatable->validateAssociatedCollection($others);

        // then
        Assert::thatArray($validatable->getErrors())->containsOnly('error1', 'error2');
        Assert::thatArray($validatable->getErrorFields())->containsOnly('errorField1', 'errorField2');
    }
}
