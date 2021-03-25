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
    private Uri $uri;

    private string $path;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
        $this->path = $this->uri->getPathWithoutPrefix();
    }

    public function findRoute(): RouteRule
    {
        $requestType = Uri::getRequestType();
        $rule = $this->findRouteRuleForMethod($requestType);
        if (!$rule) {
            throw new RouterException("No route rule found for HTTP method [{$requestType}] and URI [{$this->path}]");
        }
        return $rule;
    }

    public function findRouteRuleForMethod(string $requestType): ?RouteRule
    {
        $routeRule = Arrays::find(Route::getRoutes(), fn(RouteRule $rule) => $rule->matches($this->path, $requestType));
        if ($routeRule) {
            $routeRule->setParameters($this->path);
        }
        return $routeRule;
    }
}
