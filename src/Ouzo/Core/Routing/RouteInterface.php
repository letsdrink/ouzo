<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Routing;

interface RouteInterface
{
    public static function get($uri, $controller, $action, array $options = []);

    public static function post($uri, $controller, $action, array $options = []);

    public static function put($uri, $controller, $action, array $options = []);

    public static function delete($uri, $controller, $action, array $options = []);

    public static function any($uri, $controller, $action, array $options = []);

    public static function resource($controller, $uriPrefix);
}
