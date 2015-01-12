<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Restriction;

use BadMethodCallException;

abstract class NoValueRestriction extends Restriction
{
    public function getValues()
    {
        throw new BadMethodCallException('This type of restriction has no value');
    }
}
