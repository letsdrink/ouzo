<?php
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
