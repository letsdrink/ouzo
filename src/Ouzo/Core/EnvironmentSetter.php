<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

class EnvironmentSetter
{
    /** @var string */
    private $env = 'prod';

    public function __construct($env)
    {
        $this->env = $env;
    }

    public function set()
    {
        putenv('environment=' . $this->env);
    }
}
