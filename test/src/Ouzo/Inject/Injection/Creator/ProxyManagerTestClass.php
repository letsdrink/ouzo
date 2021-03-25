<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;

class ProxyManagerTestClass
{
    public $field;

    public function __construct()
    {
        ProxyManagerInstanceCreatorTest::$constructorInvoked = true;
    }
}