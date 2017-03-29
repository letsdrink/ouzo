<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class AutoloadNamespaces
{
    /**
     * @return array
     */
    public static function getControllerNamespace()
    {
        $controllerPath = Config::getValue('namespace', 'controller');
        return $controllerPath ? Arrays::toArray($controllerPath) : ["\\Application\\Controller\\"];
    }

    /**
     * @return string
     */
    public static function getModelNamespace()
    {
        $controllerPath = Config::getValue('namespace', 'model');
        return $controllerPath !== null ? $controllerPath : "\\Application\\Model\\";
    }

    /**
     * @return string
     */
    public static function getWidgetNamespace()
    {
        $controllerPath = Config::getValue('namespace', 'widget');
        return $controllerPath ? $controllerPath : "\\Application\\Widget\\";
    }
}
