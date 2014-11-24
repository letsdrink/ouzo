<?php

namespace Ouzo;


class AutoloadPaths {

    public static function getControllerPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'controller');
        return $controllerPath ? $controllerPath : "\\Controller\\";
    }

    public static function getModelPath()
    {
        $controllerPath = Config::getValue('autoload', 'namespace', 'model');
        return $controllerPath ? $controllerPath : "\\Model\\";
    }
} 