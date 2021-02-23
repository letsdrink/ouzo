<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Config;

use Ouzo\Config;

class ConfigOverrideProperty
{
    public function __construct(private array $keys)
    {
    }

    public function with(mixed $value): void
    {
        Config::overridePropertyArray($this->keys, $value);
    }
}
