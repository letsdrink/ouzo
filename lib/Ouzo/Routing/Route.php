<?php
namespace Ouzo\Routing;

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
        self::$routes[] = new RouteRule($method, $uri, $action);
    }

    /**
     * @return RouteRule[]
     */
    public static function getRoutes()
    {
        return self::$routes;
    }
}