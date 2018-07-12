<?php

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class ExceptionThrowMiddleware implements Interceptor
{
    public function handle($param, Chain $next)
    {
        throw new Exception("afterInitCallback");
    }
}
