<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class InterceptorOne implements Interceptor
{
    public function handle($param, Chain $next)
    {
        return $next->proceed($param . '1');
    }
}
