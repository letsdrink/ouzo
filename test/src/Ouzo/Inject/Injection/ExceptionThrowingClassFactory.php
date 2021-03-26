<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Factory;

class ExceptionThrowingClassFactory implements Factory
{
    public function create(): object
    {
        throw new Exception('Should never be invoked! It means lazy is not working.');
    }
}
