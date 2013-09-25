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