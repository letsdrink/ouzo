<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Factory;

class ClassFactory implements Factory
{
    public function create(): ClassWithNoDep
    {
        return (new ClassWithNoDep())->setThroughFactoryFlag();
    }
}
