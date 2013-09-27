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
    public static $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
    public static $validate = true;


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
        self::_addRoute(self::$methods, $uri, $action);
    }

    public static function resource($action)
    {
        self::_addRoute('GET',
            self::_createRouteUri($action),
            self::_createRouteAction($action, 'index')
        );
        self::_addRoute('GET',
            self::_createRouteUri($action, '/fresh'),
            self::_createRouteAction($action, 'fresh')
        );
        self::_addRoute('GET',
            self::_createRouteUri($action, '/:id/edit'),
            self::_createRouteAction($action, 'edit')
        );
        self::_addRoute('GET',
            self::_createRouteUri($action, '/:id'),
            self::_createRouteAction($action, 'show')
        );
        self::_addRoute('POST',
            self::_createRouteUri($action),
            self::_createRouteAction($action, 'create')
        );
        self::_addRoute('PUT',
            self::_createRouteUri($action, '/:id'),
            self::_createRouteAction($action, 'update')
        );
        self::_addRoute('PATCH',
            self::_createRouteUri($action, '/:id'),
            self::_createRouteAction($action, 'update')
        );
        self::_addRoute('DELETE',
            self::_createRouteUri($action, '/:id'),
            self::_createRouteAction($action, 'destroy')
        );
    }

    public static function allowAll($uri, $controller, $except = array())
    {
        self::_addRoute(self::$methods, $uri, $controller, false, $except);
    }

    private static function _createRouteUri($action, $suffix = '')
    {
        return '/' . $action . $suffix;
    }

    private static function _createRouteAction($controller, $action)
    {
        return $controller . '#' . $action;
    }

    private static function _addRoute($method, $uri, $action, $requireAction = true, $except = array())
    {
        if (self::$validate && self::_existRouteRule($method, $uri)) {
            $methods = is_array($method) ? implode(', ', $method) : $method;
            throw new InvalidArgumentException('Route rule for method ' . $methods . ' and URI "' . $uri . '" already exists');
        }

        $routeRule = new RouteRule($method, $uri, $action, $requireAction, $except);
        if ($routeRule->hasRequiredAction()) {
            throw new InvalidArgumentException('Route rule ' . $uri . ' required action');
        }
        self::$routes[] = $routeRule;
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