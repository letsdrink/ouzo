<?php
namespace Ouzo\Utilities;

class Functions
{
    public static function extractId()
    {
        return function ($object) {
            return $object->getId();
        };
    }

    public static function extractField($name)
    {
        return function ($object) use ($name) {
            return $object->$name;
        };
    }

    public static function extractFieldRecursively($names)
    {
        return function ($object) use ($names) {
            return Objects::getFieldRecursively($object, $names);
        };
    }

    public static function identity()
    {
        return function ($object) {
            return $object;
        };
    }

    public static function trim()
    {
        return function ($string) {
            return trim($string);
        };
    }

    public static function not($predicate)
    {
        return function ($object) use ($predicate) {
            return !$predicate($object);
        };
    }

    public static function isArray()
    {
        return function ($object) {
            return is_array($object);
        };
    }

    public static function prepend($prefix)
    {
        return function ($string) use ($prefix) {
            return $prefix . $string;
        };
    }

    public static function append($suffix)
    {
        return function ($string) use ($suffix) {
            return $string . $suffix;
        };
    }

    public static function notEmpty()
    {
        return function ($object) {
            return !empty($object);
        };
    }

    public static function notBlank()
    {
        return function ($string) {
            return trim($string);
        };
    }

    public static function formatDateTime($format = Date::DEFAULT_TIME_FORMAT)
    {
        return function ($date) use($format) {
            return Date::formatDateTime($date, $format);
        };
    }

    public static function call($function, $argument)
    {
        return call_user_func($function, $argument);
    }

    /**
     * Returns the composition of two functions.
     * composition is defined as the function h such that h(a) == A(B(a)) for each a.
    */
    public static function compose($functionA, $functionB)
    {
        return function ($input) use($functionA, $functionB) {
            return Functions::call($functionA, Functions::call($functionB, $input));
        };
    }
}