<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

class AutoloadNamespaces
{
    public static function getControllerNamespace()
    {
        $controllerPath = Config::getValue('namespace', 'controller');
        return $controllerPath ? $controllerPath : "\\Application\\Controller\\";
    }

    public static function getModelNamespace()
    {
        $controllerPath = Config::getValue('namespace', 'model');
        return $controllerPath ? $controllerPath : "\\Application\\Model\\";
    }

    public static function getWidgetNamespace()
    {
        $controllerPath = Config::getValue('namespace', 'widget');
        return $controllerPath ? $controllerPath : "\\Application\\Widget\\";
    }
}
