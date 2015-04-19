<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Api;

use Ouzo\ExceptionHandling\OuzoException;

class InternalException extends OuzoException
{
    const HTTP_CODE = 500;

    public function __construct($errors, $headers = array())
    {
        parent::__construct(self::HTTP_CODE, $errors, $headers);
    }
}
