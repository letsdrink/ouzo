<?php
namespace Ouzo\Utilities;

use Ouzo\Model;

class Objects
{
    public static function toString($var)
    {
        switch (gettype($var)) {
            case 'boolean':
                return self::booleanToString($var);
            case 'NULL':
                return "null";
            case 'string':
                return "\"$var\"";
            case 'object':
                return self::objectToString($var);
            case 'array':
                return self::arrayToString($var);
        }
        return "$var";
    }

    private static function objectToString($object)
    {
        $array = get_object_vars($object);
        $elements = self::stringifyArrayElements($array);
        return '{' . implode(', ', $elements) . '}';
    }

    private static function stringifyArrayElements($array)
    {
        $elements = array();
        $isAssociative = array_keys($array) !== range(0, sizeof($array) - 1);
        array_walk($array, function ($element, $key) use (&$elements, $isAssociative) {
            if ($isAssociative)
                $elements[] = "<$key> => " . Objects::toString($element);
            else
                $elements[] = Objects::toString($element);
        });
        return $elements;
    }

    private static function arrayToString(array $array)
    {
        $elements = self::stringifyArrayElements($array);
        return '[' . implode(', ', $elements) . ']';
    }

    public static function booleanToString($var)
    {
        return $var ? 'true' : 'false';
    }

    public static function getValueRecursively($object, $names, $default = null)
    {
        $fields = explode('->', $names);
        foreach ($fields as $field) {
            $object = self::getValueOrCallMethod($object, $field, null);
            if ($object === null) {
                return $default;
            }
        }
        return $object;
    }

    public static function getValueOrCallMethod($object, $field, $default)
    {
        $value = self::getValue($object, $field, null);
        if ($value !== null) {
            return $value;
        }
        return self::callMethod($object, $field, $default);
    }

    public static function getValue($object, $field, $default)
    {
        $model = $object instanceOf Model;
        if (!self::_fieldNotExistOrNull($model, $object, $field)) {
            return $object->$field;
        }
        return $default;
    }

    private static function _fieldNotExistOrNull($model, $object, $field)
    {
        return ($model && !$object->$field) || (!$model && !property_exists($object, $field));
    }

    public static function callMethod($object, $methodName, $default)
    {
        $name = rtrim($methodName, '()');
        if (Strings::endsWith($methodName, '()') && method_exists($object, $name)) {
            $result = $object->$name();
            return $result === null ? $default : $result;
        }
        return $default;
    }
}