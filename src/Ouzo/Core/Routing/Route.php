<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Routing;

use InvalidArgumentException;
use Ouzo\Config;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

/**
 * Routes define URLs mapping to controllers and actions.
 *
 * Sample usage:
 * <code>
 *  Route::get('/agents/index', 'agents#index'); will match: GET /agents/index
 *  Route::post('/agents/save', 'agents#save'); will match: POST /agents/save
 *  Route::resource('agents'); will mapping RESTs methods (index, fresh, edit, show, create, update, destroy)
 *  Route::any('/agents/show_numbers', 'agents#show_numbers'); will match: POST or GET /agents/show_numbers
 *  Route::allowAll('/agents', 'agents'); will mapping any methods to all actions in controller
 * </code>
 *
 * To show all routes or routes per controller:
 * <code>
 *  Route::getRoutes();
 *  Route::getRoutesForController('agents');
 * </code>
 */
class Route implements RouteInterface
{
    public static $methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
    public static $validate = true;
    public static $isDebug;

    /**
     * @var RouteRule[]
     */
    private static $routes = array();
    private static $routeKeys = array();

    public static function get($uri, $action, array $options = array())
    {
        self::addRoute('GET', $uri, $action, true, $options);
    }

    public static function post($uri, $action, array $options = array())
    {
        self::addRoute('POST', $uri, $action, true, $options);
    }

    public static function put($uri, $action, array $options = array())
    {
        self::addRoute('PUT', $uri, $action, true, $options);
    }

    public static function delete($uri, $action, array $options = array())
    {
        self::addRoute('DELETE', $uri, $action, true, $options);
    }

    public static function any($uri, $action, array $options = array())
    {
        self::addRoute(self::$methods, $uri, $action, true, $options);
    }

    public static function resource($controller)
    {
        self::addResourceRoute($controller, 'GET', '', 'index');
        self::addResourceRoute($controller, 'GET', '/fresh', 'fresh');
        self::addResourceRoute($controller, 'GET', '/:id/edit', 'edit');
        self::addResourceRoute($controller, 'GET', '/:id', 'show');
        self::addResourceRoute($controller, 'POST', '', 'create');
        self::addResourceRoute($controller, 'PUT', '/:id', 'update');
        self::addResourceRoute($controller, 'PATCH', '/:id', 'update');
        self::addResourceRoute($controller, 'DELETE', '/:id', 'destroy');
    }

    public static function allowAll($uri, $controller, $options = array())
    {
        self::addRoute(self::$methods, $uri, $controller, false, $options);
    }

    private static function addRoute($method, $uri, $action, $requireAction = true, $options = array(), $isResource = false)
    {
        $uri = self::clean($uri);
        $action = self::clean($action);

        $methods = Arrays::toArray($method);
        if (self::$isDebug && $requireAction && self::$validate && self::existRouteRule($methods, $uri)) {
            $methods = implode(', ', $methods);
            throw new InvalidArgumentException('Route rule for method ' . $methods . ' and URI "' . $uri . '" already exists');
        }

        $routeRule = new RouteRule($method, $uri, $action, $requireAction, $options, $isResource);
        if ($routeRule->hasRequiredAction()) {
            throw new InvalidArgumentException('Route rule ' . $uri . ' required action');
        }
        self::$routes[] = $routeRule;
        foreach ($methods as $method) {
            self::$routeKeys[$method . $uri] = true;
        }
    }

    private static function clean($string)
    {
        return preg_replace('/\/+/', '/', $string);
    }

    private static function existRouteRule($methods, $uri)
    {
        $routeKeys = Route::$routeKeys;
        return Arrays::any($methods, function ($method) use ($routeKeys, $uri) {
            return Arrays::keyExists($routeKeys, $method . $uri);
        });
    }

    private static function addResourceRoute($controller, $method, $uriSuffix, $action)
    {
        self::addRoute($method,
            self::createRouteUri($controller, $uriSuffix),
            self::createRouteAction($controller, $action),
            true, array(), true
        );
    }

    private static function createRouteUri($action, $suffix = '')
    {
        return '/' . $action . $suffix;
    }

    private static function createRouteAction($controller, $action)
    {
        return $controller . '#' . $action;
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

    public static function group($name, $routeFunction)
    {
        GroupedRoute::setGroupName($name);
        $routeFunction();
    }

    public static function clear()
    {
        self::$routes = array();
        self::$routeKeys = array();
    }
}

Route::$isDebug = Config::getValue('debug');
