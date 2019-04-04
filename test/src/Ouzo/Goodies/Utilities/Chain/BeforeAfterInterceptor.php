<?php

use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class BeforeAfterInterceptor implements Interceptor
{
    public function handle($param, Chain $next)
    {
        $param .= 'before';
        $chain = $next->proceed($param);
        $chain .= 'after';
        return $chain;
    }
}
