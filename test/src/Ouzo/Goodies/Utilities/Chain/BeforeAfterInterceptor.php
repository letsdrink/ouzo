<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class BeforeAfterInterceptor implements Interceptor
{
    public function handle(mixed $param, Chain $next): string
    {
        $param .= 'before';
        $chain = $next->proceed($param);
        $chain .= 'after';
        return $chain;
    }
}
