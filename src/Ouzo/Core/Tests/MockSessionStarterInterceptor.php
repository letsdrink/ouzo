<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class MockSessionStarterInterceptor implements Interceptor
{
    public function handle(mixed $param, Chain $next): mixed
    {
        $mockSessionInitializer = new MockSessionInitializer();
        $mockSessionInitializer->startSession();

        return $next->proceed($param);
    }
}