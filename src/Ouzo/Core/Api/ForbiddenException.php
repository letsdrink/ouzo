<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Api;

use Ouzo\ExceptionHandling\Error;
use Ouzo\ExceptionHandling\OuzoException;
use Ouzo\I18n;

class ForbiddenException extends OuzoException
{
    const HTTP_CODE = 403;

    public function __construct($errors)
    {
        parent::__construct(self::HTTP_CODE, $errors);
    }
}
