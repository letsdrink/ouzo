<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing;

use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class Router
{
    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var String
     */
    private $path;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
        $this->path = $this->uri->getPathWithoutPrefix();
    }

    /**
     * @return RouteRule
     * @throws RouterException
     */
    public function findRoute()
    {
        $requestType = Uri::getRequestType();
        $rule = $this->findRouteRuleForMethod($requestType);
        if (!$rule) {
            throw new RouterException('No route rule found for HTTP method [' . $requestType . '] and URI [' . $this->path . ']');
        }
        return $rule;
    }

    /**
     * @param $requestType
     * @return null|RouteRule
     */
    public function findRouteRuleForMethod($requestType)
    {
        $routeRule = Arrays::find(Route::getRoutes(), function (RouteRule $rule) use ($requestType) {
            return $rule->matches($this->path, $requestType);
        });
        if ($routeRule) {
            $routeRule->setParameters($this->path);
        }
        return $routeRule;
    }
}
