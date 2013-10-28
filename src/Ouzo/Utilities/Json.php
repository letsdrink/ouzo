<?php
namespace Ouzo\Utilities;

class Json
{
    public static function decode($string, $asArray = false)
    {
        return json_decode($string, $asArray);
    }

    public static function lastError()
    {
        return json_last_error();
    }

    public static function isJson($string)
    {
        self::decode($string);
        return self::lastError() == JSON_ERROR_NONE;
    }
}