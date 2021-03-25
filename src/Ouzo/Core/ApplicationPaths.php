<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Path;

class ApplicationPaths
{
    public static function getViewPath(): string
    {
        $controllerPath = Config::getValue('path', 'view');
        return $controllerPath ? $controllerPath : Path::join('Application', 'View');
    }

    public static function getHelperPath(): string
    {
        $controllerPath = Config::getValue('path', 'helper');
        return $controllerPath ? $controllerPath : Path::join('Application', 'Helper');
    }

    public static function getLayoutPath(): string
    {
        $controllerPath = Config::getValue('path', 'layout');
        return $controllerPath ? $controllerPath : Path::join('Application', 'Layout');
    }
}
