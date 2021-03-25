<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing;

use Closure;
use InvalidArgumentException;
use Ouzo\Config;
use Ouzo\Http\HttpMethod;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionClass;
use ReflectionMethod;

class Route implements RouteInterface
{
    public static bool $isDebug;
    public static bool $validate = true;

    private static array $methods = [
        HttpMethod::GET, HttpMethod::POST, HttpMethod::PUT, HttpMethod::PATCH, HttpMethod::DELETE, HttpMethod::OPTIONS
    ];
    /** @var RouteRule[] */
    private static array $routes = [];
    private static array $routeKeys = [];

    public static function get(string $uri, string $controller, string $action, array $options = []): void
    {
        self::addRoute(HttpMethod::GET, $uri, $controller, $action, true, $options);
    }

    public static function post(string $uri, string $controller, string $action, array $options = []): void
    {
        self::addRoute(HttpMethod::POST, $uri, $controller, $action, true, $options);
    }

    public static function put(string $uri, string $controller, string $action, array $options = []): void
    {
        self::addRoute(HttpMethod::PUT, $uri, $controller, $action, true, $options);
    }

    public static function delete(string $uri, string $controller, string $action, array $options = []): void
    {
        self::addRoute(HttpMethod::DELETE, $uri, $controller, $action, true, $options);
    }

    public static function options(string $uri, string $controller, string $action, array $options = []): void
    {
        self::addRoute(HttpMethod::OPTIONS, $uri, $controller, $action, true, $options);
    }

    public static function any(string $uri, string $controller, string $action, array $options = []): void
    {
        self::addRoute(self::$methods, $uri, $controller, $action, true, $options);
    }

    public static function resource(string $controller, string $uriPrefix): void
    {
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::GET, '', 'index');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::GET, '/fresh', 'fresh');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::GET, '/:id/edit', 'edit');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::GET, '/:id', 'show');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::POST, '', 'create');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::PUT, '/:id', 'update');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::PATCH, '/:id', 'update');
        self::addResourceRoute($controller, $uriPrefix, HttpMethod::DELETE, '/:id', 'destroy');
    }

    public static function allowAll(string $uri, string $controller, array $options = []): void
    {
        self::addRoute(self::$methods, $uri, $controller, null, false, $options);
    }

    private static function addResourceRoute(string $controller, string $uriPrefix, string $method, string $uriSuffix, string $action): void
    {
        $uri = self::createRouteUri($uriPrefix, $uriSuffix);
        self::addRoute($method, $uri, $controller, $action, true, [], true);
    }

    private static function addRoute(
        array|string $method,
        string $uri,
        string $controller,
        ?string $action,
        bool $requireAction,
        array $options,
        bool $isResource = false
    ): void
    {
        $methods = Arrays::toArray($method);
        if (self::$isDebug && $requireAction && self::$validate && self::existRouteRule($methods, $uri)) {
            $methods = implode(', ', $methods);
            throw new InvalidArgumentException("Route rule for method '{$methods}' and URI '{$uri}' already exists.");
        }

        if (self::$isDebug && !$isResource) {
            self::validateMethod($methods, $uri, $controller, $action);
        }

        $routeRule = new RouteRule($method, $uri, $controller, $action, $requireAction, $options, $isResource);
        if ($routeRule->hasRequiredAction()) {
            throw new InvalidArgumentException("Route rule '{$uri}' required action.");
        }
        self::$routes[] = $routeRule;
        foreach ($methods as $method) {
            self::$routeKeys[$method . $uri] = true;
        }
    }

    private static function existRouteRule(array $methods, string $uri): bool
    {
        $routeKeys = Route::$routeKeys;
        return Arrays::any($methods, function ($method) use ($routeKeys, $uri) {
            return Arrays::keyExists($routeKeys, $method . $uri);
        });
    }

    private static function createRouteUri(string $prefix, string $suffix = ''): string
    {
        return '/' . ltrim($prefix, '/') . $suffix;
    }

    /** @return RouteRule[] */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /** @param RouteRule[] $routes */
    public static function setRoutes(array $routes): void
    {
        self::$routes = $routes;
    }

    /** @return RouteRule[] */
    public static function getRoutesForController(string $controller): array
    {
        return Arrays::filter(self::getRoutes(), function (RouteRule $route) use ($controller) {
            return Strings::equalsIgnoreCase($route->getController(), $controller);
        });
    }

    public static function group(string $name, Closure $routeFunction): void
    {
        GroupedRoute::setGroupName($name);
        $routeFunction();
    }

    public static function clear(): void
    {
        self::$routes = [];
        self::$routeKeys = [];
    }

    private static function validateMethod(array $methodOrMethods, string $uri, string $controller, ?string $action): void
    {
        if (!is_null($action)) {
            $controllerReflection = new ReflectionClass($controller);
            $reflectionMethods = $controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC);
            if (!Arrays::any($reflectionMethods, fn($method) => $method->name === $action)) {
                $methods = implode('|', Arrays::toArray($methodOrMethods)); // Route::any() passes string[]
                throw new RouterException("Public method '$controller::$action()' missing. Route: '$methods $uri'.");
            }
        }
    }
}

Route::$isDebug = Config::getValue('debug');
