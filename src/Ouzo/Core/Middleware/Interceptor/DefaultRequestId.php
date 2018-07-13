<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\FrontController;
use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class DefaultRequestId implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
    {
        $id = uniqid();
        if (FrontController::$requestId === null) {
            FrontController::$requestId = $id;
            $requestContext->id = $id;
        }

        return $next->proceed($requestContext);
    }
}
