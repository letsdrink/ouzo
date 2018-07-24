<?php

namespace Ouzo\Tests;

use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class MockSessionStarterInterceptor implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
    {
        $mockSessionInitializer = new MockSessionInitializer();
        $mockSessionInitializer->startSession();

        return $next->proceed($requestContext);
    }
}