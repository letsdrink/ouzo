<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

class EnvironmentSetter
{
    public function __construct(private string $env = 'prod')
    {
    }

    public function set()
    {
        putenv("environment={$this->env}");
    }
}
