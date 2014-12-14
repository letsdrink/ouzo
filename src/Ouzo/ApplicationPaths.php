<?php
namespace Ouzo;

use Ouzo\Utilities\Path;

class ApplicationPaths
{

    public static function getViewPath()
    {
        $controllerPath = Config::getValue('path', 'view');
        return $controllerPath ? $controllerPath : Path::join('Application', 'View');
    }

    public static function getHelperPath()
    {
        $controllerPath = Config::getValue('path', 'helper');
        return $controllerPath ? $controllerPath : Path::join('Application', 'Helper');
    }

    public static function getLayoutPath()
    {
        $controllerPath = Config::getValue('path', 'layout');
        return $controllerPath ? $controllerPath : Path::join('Application', 'Layout');
    }
}
