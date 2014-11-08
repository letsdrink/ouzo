<?php
namespace Ouzo\Utilities;

use ReflectionObject;

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
        if (method_exists($object, '__toString')) {
            return (string)$object;
        }
        $array = get_object_vars($object);
        $elements = self::stringifyArrayElements($array);
        return get_class($object) . ' {' . implode(', ', $elements) . '}';
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

    public static function setValueRecursively($object, $names, $value)
    {
        $fields = explode('->', $names);
        $destinationField = array_pop($fields);
        $destinationObject = self::getValueRecursively($object, implode('->', $fields));
        if ($destinationObject !== null) {
            $destinationObject->$destinationField = $value;
        }
    }

    public static function getValueRecursively($object, $names, $default = null, $accessPrivate = false)
    {
        $fields = array_filter(explode('->', $names));
        foreach ($fields as $field) {
            $object = self::getValueOrCallMethod($object, $field, null, $accessPrivate);
            if ($object === null) {
                return $default;
            }
        }
        return $object;
    }

    public static function getValueOrCallMethod($object, $field, $default, $accessPrivate = false)
    {
        $value = self::getValue($object, $field, null, $accessPrivate);
        if ($value !== null) {
            return $value;
        }
        return self::callMethod($object, $field, $default);
    }

    public static function getValue($object, $field, $default = null, $accessPrivate = false)
    {
        if (isset($object->$field)) {
            return $object->$field;
        }
        if ($accessPrivate) {
            $class = new ReflectionObject($object);
            if ($class->hasProperty($field)) {
                $property = $class->getProperty($field);
                $property->setAccessible(true);
                return $property->getValue($object);
            }
        }
        return $default;
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