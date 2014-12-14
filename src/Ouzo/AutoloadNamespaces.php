<?php
namespace Ouzo;

class AutoloadNamespaces
{
    public static function getControllerNamespace()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'controller');
        return $controllerPath ? $controllerPath : "\\Application\\Controller\\";
    }

    public static function getModelNamespace()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'model');
        return $controllerPath ? $controllerPath : "\\Application\\Model\\";
    }
}
