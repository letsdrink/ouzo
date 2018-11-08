<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Factory;

class ClassFactory implements Factory
{

    public function create()
    {
        return new ClassCreatedByFactory();
    }
}