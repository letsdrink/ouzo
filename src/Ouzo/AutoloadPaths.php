<?php
namespace Ouzo;

use Ouzo\Utilities\Path;

class AutoloadPaths
{
    public static function getControllerPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'controller');
        return $controllerPath ? $controllerPath : "\\Application\\Controller\\";
    }

    public static function getModelPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'model');
        return $controllerPath ? $controllerPath : "\\Application\\Model\\";
    }

    public static function getViewPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'view');
        return $controllerPath ? $controllerPath : Path::join('Application', 'View');
    }

    public static function getHelperPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'helper');
        return $controllerPath ? $controllerPath : Path::join('Application', 'Helper');
    }

    public static function getLayoutPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'layout');
        return $controllerPath ? $controllerPath : Path::join('Application', 'Layout');
    }
}
