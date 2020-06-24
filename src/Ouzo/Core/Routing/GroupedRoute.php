<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Routing;

class GroupedRoute implements RouteInterface
{
    private static $name;

    public static function setGroupName($name)
    {
        self::$name = $name;
    }

    public static function get($uri, $controller, $action, array $options = [])
    {
        Route::get(self::uri($uri), $controller, $action, $options);
    }

    public static function post($uri, $controller, $action, array $options = [])
    {
        Route::post(self::uri($uri), $controller, $action, $options);
    }

    public static function put($uri, $controller, $action, array $options = [])
    {
        Route::put(self::uri($uri), $controller, $action, $options);
    }

    public static function delete($uri, $controller, $action, array $options = [])
    {
        Route::delete(self::uri($uri), $controller, $action, $options);
    }

    public static function any($uri, $controller, $action, array $options = [])
    {
        Route::any(self::uri($uri), $controller, $action, $options);
    }

    public static function resource($controller, $uriPrefix)
    {
        Route::resource($controller, self::uri($uriPrefix));
    }

    private static function uri($uri)
    {
        return '/' . self::$name . '/' . ltrim($uri, '/');
    }
}
