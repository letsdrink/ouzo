<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Middleware\Interceptor;

use Ouzo\Db\Stats;
use Ouzo\Request\RequestContext;
use Ouzo\Session;
use Ouzo\Uri;
use Ouzo\Utilities\Chain\Chain;
use Ouzo\Utilities\Chain\Interceptor;

class ResetStats implements Interceptor
{
    /**
     * @param RequestContext $requestContext
     * @param Chain $next
     * @return Chain
     */
    public function handle($requestContext, Chain $next)
    {
        if (!isset($_SESSION['reset_stats']) || $_SESSION['reset_stats']) {
            Stats::reset();
        }

        $chain = $next->proceed($requestContext);

        Session::set('reset_stats', $requestContext->getCurrentControllerObject()->getStatusResponse() == 'show' && !Uri::isAjax());

        return $chain;
    }
}
