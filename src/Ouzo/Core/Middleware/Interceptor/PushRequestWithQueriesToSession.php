<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\Config;
use Ouzo\Db\Stats;
use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class PushRequestWithQueriesToSession implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
    {
        if (Config::getValue('debug')) {
            Stats::traceHttpRequest($requestContext->getCurrentControllerObject()->params);
        }

        return $next->proceed($requestContext);
    }
}
