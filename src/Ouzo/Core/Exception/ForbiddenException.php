<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Exception;

use Ouzo\ExceptionHandling\Error;
use Ouzo\ExceptionHandling\OuzoException;

class ForbiddenException extends OuzoException
{
    const HTTP_CODE = 403;

    /**
     * @param Error[]|\Error $errors
     */
    public function __construct($errors)
    {
        parent::__construct(self::HTTP_CODE, "Forbidden.", $errors);
    }
}
