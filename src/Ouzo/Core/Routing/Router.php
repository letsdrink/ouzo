<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Routing;

use Exception;
use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class Router
{
    /**
     * @var Uri
     */
    private $uri;

    public function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return RouteRule
     * @throws RouterException
     */
    public function findRoute()
    {
        $path = $this->uri->getPathWithoutPrefix();
        $requestType = Uri::getRequestType();
        $rule = $this->findRouteRule($path, $requestType);
        if (!$rule) {
            throw new RouterException('No route rule found for HTTP method [' . $requestType . '] and URI [' . $path . ']');
        }
        $rule->setParameters($path);
        return $rule;
    }

    /**
     * @param $path
     * @param $requestType
     * @return RouteRule
     */
    private function findRouteRule($path, $requestType)
    {
        return Arrays::find(Route::getRoutes(), function (RouteRule $rule) use ($path, $requestType) {
            return $rule->matches($path, $requestType);
        });
    }
}

class RouterException extends Exception
{
}
