<?php
namespace Ouzo\Utilities;

class Arrays
{
    public static function all(array $elements, $predicate)
    {
        foreach ($elements as $element) {
            if (!Functions::call($predicate, $element)) {
                return false;
            }
        }
        return true;
    }

    public static function toMap(array $elements, $keyFunction, $valueFunction = null)
    {
        if ($valueFunction == null) {
            $valueFunction = Functions::identity();
        }

        $keys = array_map($keyFunction, $elements);
        $values = array_map($valueFunction, $elements);
        return empty($keys) ? array() : array_combine($keys, $values);
    }

    static function flatten(array $elements)
    {
        $return = array();
        array_walk_recursive($elements, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }

    static public function findKeyByValue(array $elements, $value)
    {
        if ($value === 0) {
            $value = '0';
        }
        foreach ($elements as $key => $item) {
            if ($item == $value) {
                return $key;
            }
        }
        return FALSE;
    }

    public static function any(array $elements, $predicate)
    {
        foreach ($elements as $element) {
            if (Functions::call($predicate, $element)) {
                return true;
            }
        }
        return false;
    }

    public static function first(array $elements)
    {
        if (empty($elements)) {
            throw new \InvalidArgumentException('empty array');
        }
        $keys = array_keys($elements);
        return $elements[$keys[0]];
    }

    public static function last(array $elements)
    {
        if (empty($elements)) {
            throw new \InvalidArgumentException('empty array');
        }
        return end($elements);
    }

    public static function firstOrNull(array $elements)
    {
        return empty($elements) ? null : self::first($elements);
    }

    public static function getValue(array $elements, $key, $default = null)
    {
        return isset($elements[$key]) ? $elements[$key] : $default;
    }

    public static function filterByAllowedKeys(array $elements, $allowedKeys)
    {
        return array_intersect_key($elements, array_flip($allowedKeys));
    }

    public static function filterByKeys(array $elements, $predicate)
    {
        $allowedKeys = array_filter(array_keys($elements), $predicate);
        return self::filterByAllowedKeys($elements, $allowedKeys);
    }

    public static function groupBy(array $elements, $keyFunction, $orderField = null)
    {
        $map = array();
        if (!empty($orderField)) {
            $elements = self::orderBy($elements, $orderField);
        }
        foreach ($elements as $element) {
            $key = Functions::call($keyFunction, $element);
            $map[$key][] = $element;
        }
        return $map;
    }

    public static function orderBy(array $elements, $orderField)
    {
        usort($elements, function ($a, $b) use ($orderField) {
            return $a->$orderField < $b->$orderField ? -1 : 1;
        });
        return $elements;
    }

    public static function mapKeys(array $elements, $function)
    {
        $newArray = array();
        foreach ($elements as $oldKey => $value) {
            $newKey = Functions::call($function, $oldKey);
            $newArray[$newKey] = $value;
        }
        return $newArray;
    }

    public static function map(array $elements, $function)
    {
        return array_map($function, $elements);
    }

    public static function filter(array $elements, $function)
    {
        return array_filter($elements, $function);
    }

    public static function toArray($element)
    {
        return $element ? is_array($element) ? $element : array($element) : array();
    }

    public static function randElement($elements)
    {
        return $elements ? $elements[array_rand($elements)] : null;
    }

    public static function combine(array $keys, array $values)
    {
        if (!empty($keys) && !empty($values)) {
            return array_combine($keys, $values);
        }
        return array();
    }

    public static function keyExists(array $elements, $key)
    {
        return array_key_exists($key, $elements);
    }
}