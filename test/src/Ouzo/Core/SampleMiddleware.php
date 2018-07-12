<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class SampleMiddleware implements Interceptor
{
    /** @inheritdoc */
    public function handle($request, Chain $next)
    {
        $request->forTestPurposesOnly = 'SampleMiddleware';
        return $next->proceed($request);
    }
}
