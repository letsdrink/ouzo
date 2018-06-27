<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Config;

use Ouzo\Config;

class ConfigOverrideProperty
{
    /** @var array */
    private $keys;

    /**
     * @param array $keys
     */
    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    /**
     * @param string|array $value
     * @return void
     */
    public function with($value)
    {
        Config::overridePropertyArray($this->keys, $value);
    }
}
