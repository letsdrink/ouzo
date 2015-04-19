<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Request;

class RequestContext
{
    private static $_currentController;
    private static $_currentControllerObject;

    public static function getCurrentController()
    {
        return self::$_currentController;
    }

    public static function setCurrentController($currentController)
    {
        self::$_currentController = $currentController;
    }

    public static function getCurrentControllerObject()
    {
        return self::$_currentControllerObject;
    }

    public static function setCurrentControllerObject($currentControllerObject)
    {
        self::$_currentControllerObject = $currentControllerObject;
    }
}
