<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\Logger\Logger;
use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class LogRequest implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
    {
        Logger::getLogger(__CLASS__)
            ->info('[Request:/%s/%s]', [$requestContext->getCurrentController(), $requestContext->getCurrentAction()]);

        return $next->proceed($requestContext);
    }
}
