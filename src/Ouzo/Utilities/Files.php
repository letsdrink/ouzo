<?php

namespace Ouzo\Utilities;

use Exception;

class Files
{

    public static function loadIfExists($path, $loadOnce = true)
    {
        if (file_exists($path)) {
            self::_requireWithoutInspection($path, $loadOnce);
            return true;
        }
        return false;
    }

    public static function load($path, $loadOnce = true)
    {
        if (!self::loadIfExists($path, $loadOnce)) {
            throw new FileNotFoundException('Cannot load file: ' . $path);
        }
    }

    private static function _requireWithoutInspection($path, $loadOnce)
    {
        if ($loadOnce) {
            /** @noinspection PhpIncludeInspection */
            require_once($path);
        } else {
            /** @noinspection PhpIncludeInspection */
            require($path);
        }
    }
}

class FileNotFoundException extends Exception
{
}