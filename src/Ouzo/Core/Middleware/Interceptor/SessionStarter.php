<?php

namespace Ouzo\Middleware\Interceptor;

use Ouzo\SessionInitializer;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class SessionStarter implements Interceptor
{
    public function handle(mixed $param, Chain $next): mixed
    {
        $sessionInitializer = new SessionInitializer();
        $sessionInitializer->startSession();

        return $next->proceed($param);
    }
}