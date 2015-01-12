<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;

abstract class SingleValueRestriction extends Restriction
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValues()
    {
        return $this->value;
    }
}
