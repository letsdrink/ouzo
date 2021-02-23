<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class ExceptionThrowMiddleware implements Interceptor
{
    public function handle(mixed $param, Chain $next): mixed
    {
        throw new Exception("afterInitCallback");
    }
}
