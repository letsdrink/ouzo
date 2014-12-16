<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class ContentType
{
    private static $contentType;

    public static function set($contentType)
    {
        self::$contentType = $contentType;
    }

    public static function init()
    {
        self::$contentType = self::getFromServer();
    }

    public static function getFromServer()
    {
        return Arrays::first(explode(';', Arrays::getValue($_SERVER, 'CONTENT_TYPE')));
    }

    public static function value()
    {
        return self::$contentType;
    }
}
