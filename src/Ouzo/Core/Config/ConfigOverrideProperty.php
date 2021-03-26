<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Config;

use Ouzo\Config;

class ConfigOverrideProperty
{
    /** @var string[] $keys */
    public function __construct(private array $keys)
    {
    }

    public function with(mixed $value): void
    {
        Config::overridePropertyArray($this->keys, $value);
    }
}
