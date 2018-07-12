<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware;

use Ouzo\Logger\Logger;
use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class LogRequest implements Interceptor
{
    /**
     * @param RequestContext $request
     * @param Chain $next
     * @return Chain
     */
    public function handle($request, Chain $next)
    {
        Logger::getLogger(__CLASS__)
            ->info('[Request:/%s/%s]', [$request->getCurrentController(), $request->getCurrentAction()]);
        return $next->proceed($request);
    }
}
