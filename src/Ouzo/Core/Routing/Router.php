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
    private $_uri;

    public function __construct(Uri $uri)
    {
        $this->_uri = $uri;
    }

    /**
     * @return RouteRule
     * @throws RouterException
     */
    public function findRoute()
    {
        $path = $this->_uri->getPathWithoutPrefix();
        $rule = $this->_findRouteRule($path);
        if (!$rule) {
            throw new RouterException('No route rule found for HTTP method [' . Uri::getRequestType() . '] and URI [' . $path . ']');
        }
        $rule->setParameters($path);
        return $rule;
    }

    /**
     * @param $path
     * @return RouteRule
     */
    private function _findRouteRule($path)
    {
        return Arrays::find(Route::getRoutes(), function (RouteRule $rule) use ($path) {
            return $rule->matches($path);
        });
    }
}

class RouterException extends Exception
{
}
