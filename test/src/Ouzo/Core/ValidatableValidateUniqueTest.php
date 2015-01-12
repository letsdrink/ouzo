<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Validatable;

class UniquenessValidatable extends Validatable
{
    private $values;

    public function __construct($values)
    {
        $this->values = $values;
    }

    public function validate()
    {
        parent::validate();
        $this->validateUnique($this->values, "error");
    }
}


class ValidatableValidateUniqueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldBeValidIfUniqueValues()
    {
        //given
        $validatable = new UniquenessValidatable(array('a', 'b'));

        //when
        $valid = $validatable->isValid();

        //then
        $this->assertTrue($valid);
    }

    /**
     * @test
     */
    public function shouldNotBeValidIfNonUniqueValues()
    {
        //given
        $validatable = new UniquenessValidatable(array('a', 'b', 'a'));

        //when
        $valid = $validatable->isValid();

        //then
        $this->assertFalse($valid);
    }
}
