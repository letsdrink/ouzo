<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class SampleMiddleware implements Interceptor
{
    private string $data;

    public function handle(mixed $param, Chain $next): mixed
    {
        $this->data = 'SampleMiddleware';
        return $next->proceed($param);
    }

    public function getData(): string
    {
        return $this->data;
    }
}
