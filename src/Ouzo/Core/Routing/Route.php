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
use ReflectionClass;
use ReflectionMethod;

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
    public static $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
    public static $validate = true;
    public static $isDebug;

    /**
     * @var RouteRule[]
     */
    private static $routes = [];
    private static $routeKeys = [];

    public static function get($uri, $controller, $action, array $options = [])
    {
        self::addRoute('GET', $uri, $controller, $action, true, $options);
    }

    public static function post($uri, $controller, $action, array $options = [])
    {
        self::addRoute('POST', $uri, $controller, $action, true, $options);
    }

    public static function put($uri, $controller, $action, array $options = [])
    {
        self::addRoute('PUT', $uri, $controller, $action, true, $options);
    }

    public static function delete($uri, $controller, $action, array $options = [])
    {
        self::addRoute('DELETE', $uri, $controller, $action, true, $options);
    }

    public static function options($uri, $controller, $action, array $options = [])
    {
        self::addRoute('OPTIONS', $uri, $controller, $action, true, $options);
    }

    public static function any($uri, $controller, $action, array $options = [])
    {
        self::addRoute(self::$methods, $uri, $controller, $action, true, $options);
    }

    public static function resource($controller, $uriPrefix)
    {
        self::addResourceRoute($controller, $uriPrefix, 'GET', '', 'index');
        self::addResourceRoute($controller, $uriPrefix, 'GET', '/fresh', 'fresh');
        self::addResourceRoute($controller, $uriPrefix, 'GET', '/:id/edit', 'edit');
        self::addResourceRoute($controller, $uriPrefix, 'GET', '/:id', 'show');
        self::addResourceRoute($controller, $uriPrefix, 'POST', '', 'create');
        self::addResourceRoute($controller, $uriPrefix, 'PUT', '/:id', 'update');
        self::addResourceRoute($controller, $uriPrefix, 'PATCH', '/:id', 'update');
        self::addResourceRoute($controller, $uriPrefix, 'DELETE', '/:id', 'destroy');
    }

    public static function allowAll($uri, $controller, $options = [])
    {
        self::addRoute(self::$methods, $uri, $controller, null, false, $options);
    }

    private static function addRoute($method, $uri, $controller, $action = null, $requireAction = true, $options = [], $isResource = false)
    {
        $methods = Arrays::toArray($method);
        if (self::$isDebug && $requireAction && self::$validate && self::existRouteRule($methods, $uri)) {
            $methods = implode(', ', $methods);
            throw new InvalidArgumentException('Route rule for method ' . $methods . ' and URI "' . $uri . '" already exists');
        }

        if (self::$isDebug) {
            self::validateMethod($method, $uri, $controller, $action);
        }

        $routeRule = new RouteRule($method, $uri, $controller, $action, $requireAction, $options, $isResource);
        if ($routeRule->hasRequiredAction()) {
            throw new InvalidArgumentException('Route rule ' . $uri . ' required action');
        }
        self::$routes[] = $routeRule;
        foreach ($methods as $method) {
            self::$routeKeys[$method . $uri] = true;
        }
    }

    private static function existRouteRule($methods, $uri)
    {
        $routeKeys = Route::$routeKeys;
        return Arrays::any($methods, function ($method) use ($routeKeys, $uri) {
            return Arrays::keyExists($routeKeys, $method . $uri);
        });
    }

    private static function addResourceRoute($controller, $uriPrefix, $method, $uriSuffix, $action)
    {
        $uri = self::createRouteUri($uriPrefix, $uriSuffix);
        self::addRoute($method, $uri, $controller, $action, true, [], true);
    }

    private static function createRouteUri($prefix, $suffix = '')
    {
        return '/' . ltrim($prefix, '/') . $suffix;
    }

    /**
     * @return RouteRule[]
     */
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * @param RouteRule[] $routes
     */
    public static function setRoutes($routes)
    {
        self::$routes = $routes;
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
        self::$routes = [];
        self::$routeKeys = [];
    }

    private static function validateMethod($method, $uri, $controller, $action)
    {
        if ($action) {
            $controllerReflection = new ReflectionClass($controller);
            $methods = $controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC);
            if (!Arrays::keyExists($methods, $action)) {
                throw new RouterException("Public method '$controller::$action()' missing. Route: '$method $uri'.");
            }
        }
    }
}

Route::$isDebug = Config::getValue('debug');
