<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Routing;

interface RouteInterface
{
    public static function get($uri, $action, array $options = array());

    public static function post($uri, $action, array $options = array());

    public static function put($uri, $action, array $options = array());

    public static function delete($uri, $action, array $options = array());

    public static function any($uri, $action, array $options = array());

    public static function resource($controller);
}
