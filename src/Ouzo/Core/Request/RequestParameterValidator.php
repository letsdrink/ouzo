<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Request;


use Exception;
use Ouzo\ExceptionHandling\ValidationError;

class RequestParameterValidator
{
    /**
     * @return ValidationError[]
     */
    public function validate(object $object): array
    {
        throw new Exception('Provide request parameter validator implementation if you want to use this feature');
    }
}