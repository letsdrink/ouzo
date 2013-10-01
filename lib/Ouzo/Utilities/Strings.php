<?php
namespace Ouzo\Utilities;

class Strings
{
    public static function underscoreToCamelCase($str)
    {
        $words = explode('_', strtolower($str));
        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }
        return $return;
    }

    public static function camelCaseToUnderscore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    public static function removePrefix($string, $prefix)
    {
        if (self::startsWith($string, $prefix)) {
            $string = substr($string, strlen($prefix));
        }
        return $string;
    }

    public static function removePrefixes($string, array $prefixes)
    {
        return array_reduce($prefixes, function ($string, $prefix) {
            return Strings::removePrefix($string, $prefix);
        }, $string);
    }

    public static function startsWith($string, $prefix)
    {
        return $string && $prefix && strpos($string, $prefix) === 0;
    }

    public static function endsWith($string, $suffix)
    {
        return $string && $suffix && substr($string, -strlen($suffix)) === $suffix;
    }

    public static function equalsIgnoreCase($string1, $string2)
    {
        return strtolower($string1) == strtolower($string2);
    }
}