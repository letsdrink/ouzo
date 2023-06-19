<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\FrontController;
use Ouzo\Request\RequestContext;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class DefaultRequestId implements Interceptor
{
    public function handle(mixed $param, Chain $next): mixed
    {
        return $this->handleRequestContext($param, $next);
    }

    private function handleRequestContext(RequestContext $requestContext, Chain $next): mixed
    {
        $id = uniqid();
        if (FrontController::$requestId === null) {
            FrontController::$requestId = $id;
            $requestContext->setId($id);
        }

        return $next->proceed($requestContext);
    }
}
