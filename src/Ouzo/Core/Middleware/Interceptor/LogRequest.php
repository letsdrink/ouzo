<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\Logger\Logger;
use Ouzo\Request\RequestContext;
use Ouzo\Uri;
use Ouzo\Uri\PathProvider;
use Ouzo\Uri\PathProviderInterface;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class LogRequest implements Interceptor
{
    private $pathProvider;

    /**
     * @Inject
     */
    public function __construct(PathProviderInterface $pathProvider)
    {
        $this->pathProvider = $pathProvider;
    }

    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
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
