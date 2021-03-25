<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Routing;

interface RouteInterface
{
    public static function get(string $uri, string $controller, string $action, array $options = []): void;

    public static function post(string $uri, string $controller, string $action, array $options = []): void;

    public static function put(string $uri, string $controller, string $action, array $options = []): void;

    public static function delete(string $uri, string $controller, string $action, array $options = []): void;

    public static function any(string $uri, string $controller, string $action, array $options = []): void;

    public static function resource(string $controller, string $uriPrefix): void;
}
