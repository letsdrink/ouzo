<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Validatable;
use PHPUnit\Framework\TestCase;

class UniquenessValidatable extends Validatable
{
    public function __construct(private array $values)
    {
    }

    public function validate(): void
    {
        parent::validate();
        $this->validateUnique($this->values, "error");
    }
}


class ValidatableValidateUniqueTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeValidIfUniqueValues()
    {
        //given
        $validatable = new UniquenessValidatable(['a', 'b']);

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
        $validatable = new UniquenessValidatable(['a', 'b', 'a']);

        //when
        $valid = $validatable->isValid();

        //then
        $this->assertFalse($valid);
    }
}
