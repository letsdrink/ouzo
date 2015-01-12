<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Config;

use Ouzo\Config;

class ConfigOverrideProperty
{
    private $keys;

    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    public function with($value)
    {
        Config::overridePropertyArray($this->keys, $value);
    }
}
