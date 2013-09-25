<?php
namespace Ouzo\Routing;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Route
{
    /**
     * @var RouteRule[]
     */
    public static $routes = array();

    public static function get($uri, $action)
    {
        self::_addRoute('GET', $uri, $action);
    }

    public static function post($uri, $action)
    {
        self::_addRoute('POST', $uri, $action);
    }

    public static function any($uri, $action)
    {
        $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
        self::_addRoute($methods, $uri, $action);
    }

    private static function _addRoute($method, $uri, $action)
    {
        if (self::_existRouteRule($method, $uri)) {
            throw new InvalidArgumentException('Route roule for method ' . $method . ' and URI "' . $uri . '" already exists');
        }
        self::$routes[] = new RouteRule($method, $uri, $action);
    }

    private static function _existRouteRule($method, $uri)
    {
        return Arrays::any(self::getRoutes(), function (RouteRule $routeRule) use ($method, $uri) {
            return $routeRule->getMethod() == $method && $routeRule->getUri() == $uri;
        });
    }

    /**
     * @return RouteRule[]
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function getRoutesForController($controller)
    {
        return Arrays::filter(self::getRoutes(), function (RouteRule $route) use ($controller) {
            return Strings::equalsIgnoreCase($route->getController(), $controller);
        });
    }
}