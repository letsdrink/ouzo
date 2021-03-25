<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing;

class GroupedRoute implements RouteInterface
{
    private static string $name;

    public static function setGroupName(string $name): void
    {
        self::$name = $name;
    }

    public static function get(string $uri, string $controller, string $action, array $options = []): void
    {
        Route::get(self::uri($uri), $controller, $action, $options);
    }

    public static function post(string $uri, string $controller, string $action, array $options = []): void
    {
        Route::post(self::uri($uri), $controller, $action, $options);
    }

    public static function put(string $uri, string $controller, string $action, array $options = []): void
    {
        Route::put(self::uri($uri), $controller, $action, $options);
    }

    public static function delete(string $uri, string $controller, string $action, array $options = []): void
    {
        Route::delete(self::uri($uri), $controller, $action, $options);
    }

    public static function any(string $uri, string $controller, string $action, array $options = []): void
    {
        Route::any(self::uri($uri), $controller, $action, $options);
    }

    public static function resource(string $controller, string $uriPrefix): void
    {
        Route::resource($controller, self::uri($uriPrefix));
    }

    private static function uri(string $uri): string
    {
        $prefix = self::$name;
        $sanitizedUri = ltrim($uri, '/');
        return "/{$prefix}/{$sanitizedUri}";
    }
}
