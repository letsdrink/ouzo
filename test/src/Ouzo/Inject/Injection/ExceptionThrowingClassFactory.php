<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Factory;

class ExceptionThrowingClassFactory implements Factory
{
    public function create()
    {
        throw new Exception('Should never be invoked! It means lazy is not working.');
    }
}
