<?php
namespace Ouzo\Request;

class RequestContext
{
    private static $_currentController;

    public static function getCurrentController()
    {
        return self::$_currentController;
    }

    public static function setCurrentController($currentController)
    {
        self::$_currentController = $currentController;
    }
}