<?php
namespace Ouzo\Middleware\Interceptor;

use Ouzo\Request\RequestContext;
use Ouzo\SessionInitializer;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class SessionStarter implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
    {
        $sessionInitializer = new SessionInitializer();
        $sessionInitializer->startSession();

        return $next->proceed($requestContext);
    }
}