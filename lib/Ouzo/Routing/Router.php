<?php
namespace Ouzo\Routing;

use Exception;
use Ouzo\Utilities\Arrays;

class Router
{
    private $_uri;

    public function __construct($uri)
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
        return $routeRule;
    }

    private function _findRouteRule()
    {
        $routeRules = Route::getRoutes();
        $uri = $this->_uri;
        $filtered = Arrays::filter($routeRules, function(RouteRule $routeRule) use ($uri){
            return $routeRule->isMatching($uri);
        });
        return $filtered ? Arrays::first($filtered) : null;
    }
}

class RouterException extends Exception
{
}