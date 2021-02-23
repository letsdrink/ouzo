<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\Injection\Annotation\Inject;
use Ouzo\Logger\Logger;
use Ouzo\Request\RequestContext;
use Ouzo\Uri;
use Ouzo\Uri\PathProviderInterface;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class LogRequest implements Interceptor
{
    /**
     * @Inject
     */
    public function __construct(private PathProviderInterface $pathProvider)
    {
    }

    public function handle(mixed $param, Chain $next): mixed
    {
        return $this->handleRequestContext($param, $next);
    }

    private function handleRequestContext(RequestContext $requestContext, Chain $next): mixed
    {
        Logger::getLogger(__CLASS__)->info('[Action: %s#%s] [Request: %s %s]', [
            $requestContext->getCurrentController(),
            $requestContext->getCurrentAction(),
            Uri::getRequestType(),
            $this->pathProvider->getPath()
        ]);

        return $next->proceed($requestContext);
    }
}
