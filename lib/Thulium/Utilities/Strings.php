<?php

namespace Thulium\Utilities;

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

    public static function changePolishChars($string)
    {
        $polishChars = array('ą', 'ż', 'ś', 'ź', 'ę', 'ć', 'ń', 'ó', 'ł', 'Ą', 'Ż', 'Ś', 'Ź', 'Ę', 'Ć', 'Ń', 'Ó', 'Ł');
        $replacment = array('a', 'z', 's', 'z', 'e', 'c', 'n', 'o', 'l', 'A', 'Z', 'S', 'Z', 'E', 'C', 'N', 'O', 'L');
        return str_replace($polishChars, $replacment, $string);
    }
}