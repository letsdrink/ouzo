<?php
namespace Ouzo\Utilities;

use Exception;

class Files
{
    public static function loadIfExists($path, $loadOnce = true)
    {
        if (self::exists($path)) {
            self::_require($path, $loadOnce);
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

    private static function _require($path, $loadOnce)
    {
        if ($loadOnce) {
            /** @noinspection PhpIncludeInspection */
            require_once($path);
        } else {
            /** @noinspection PhpIncludeInspection */
            require($path);
        }
    }

    public static function delete($path)
    {
        if (!self::exists($path)) {
            throw new FileNotFoundException('Cannot find file: ' . $path);
        }
        return unlink($path);
    }

    public static function move($sourcePath, $destinationPath)
    {
        if (!self::exists($sourcePath)) {
            throw new FileNotFoundException('Cannot find source file: ' . $sourcePath);
        }
        return rename($sourcePath, $destinationPath);
    }

    public static function convertUnitFileSize($size)
    {
        $units = array(" B", " KB", " MB", " GB");
        $calculatedSize = $size;
        $unit = Arrays::first($units);
        if ($size) {
            $calculatedSize = round($size / pow(1024, ($i = (int)floor(log($size, 1024)))), 2);
            $unit = $units[$i];
        }
        return $calculatedSize . $unit;
    }

    public static function size($path)
    {
        return (int)self::exists($path) ? filesize($path) : 0;
    }

    public static function exists($path)
    {
        return file_exists($path);
    }
}

class FileNotFoundException extends Exception
{
}