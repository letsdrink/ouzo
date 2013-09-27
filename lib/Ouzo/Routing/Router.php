<?php
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
        $routeRule = $this->_findRouteRule();
        if (!$routeRule) {
            throw new RouterException('No route rule found.');
        }
        $routeRule->setParameters($this->_uri->getPathWithoutPrefix());
        return $routeRule;
    }

    /**
     * @return RouteRule
     */
    private function _findRouteRule()
    {
        $routeRules = Route::getRoutes();
        $uri = $this->_uri;
        $filtered = Arrays::filter($routeRules, function (RouteRule $routeRule) use ($uri) {
            return $routeRule->matches($uri->getPathWithoutPrefix());
        });
        return Arrays::firstOrNull($filtered);
    }
}

class RouterException extends Exception
{
}