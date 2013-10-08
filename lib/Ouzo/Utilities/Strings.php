<?php
namespace Ouzo\Utilities;

class Strings
{
    /**
     * Changes underscored string to the camel case.
     *
     * Example:
     * <code>
     * $string = 'lannisters_always_pay_their_debts';
     * $camelcase = Strings::underscoreToCamelCase($string);
     * </code>
     *
     * @param $string
     * @return string
     */
    public static function underscoreToCamelCase($string)
    {
        $words = explode('_', strtolower($string));
        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }
        return $return;
    }

    /**
     * Changes camel case string to underscored.
     *
     * Example:
     * <code>
     * $string = 'LannistersAlwaysPayTheirDebts';
     * $underscored = Strings::camelCaseToUnderscore($string);
     * </code>
     *
     * @param $string
     * @return string
     */
    public static function camelCaseToUnderscore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    /**
     * Returns a new string without the given prefix.
     *
     * Example:
     * <code>
     * $string = 'prefixRest';
     * $withoutPrefix = Strings::removePrefix($string, 'prefix');
     * </code>
     *
     * @param $string
     * @param $prefix
     * @return string
     */
    public static function removePrefix($string, $prefix)
    {
        if (self::startsWith($string, $prefix)) {
            return substr($string, strlen($prefix));
        }
        return $string;
    }

    /**
     * Removes prefixes defined in array from string.
     *
     * Example:
     * <code>
     * $string = 'prefixRest';
     * $withoutPrefix = Strings::removePrefixes($string, array('pre', 'fix'));
     * </code>
     *
     * @param $string
     * @param array $prefixes
     * @return mixed
     */
    public static function removePrefixes($string, array $prefixes)
    {
        return array_reduce($prefixes, function ($string, $prefix) {
            return Strings::removePrefix($string, $prefix);
        }, $string);
    }

    /**
     * Method checks if string starts with $prefix.
     *
     * Example:
     * <code>
     * $string = 'prefixRest';
     * $result = Strings::startsWith($string, 'prefix');
     * </code>
     *
     * @param $string
     * @param $prefix
     * @return bool
     */
    public static function startsWith($string, $prefix)
    {
        return $string && $prefix && strpos($string, $prefix) === 0;
    }

    /**
     * Method checks if string ends with $suffix.
     *
     * Example:
     * <code>
     * $string = 'StringSuffix';
     * $result = Strings::endsWith($string, 'Suffix');
     * </code>
     *
     * @param $string
     * @param $suffix
     * @return bool
     */
    public static function endsWith($string, $suffix)
    {
        return $string && $suffix && substr($string, -strlen($suffix)) === $suffix;
    }

    /**
     * Determines whether two strings contain the same data, ignoring the case of the letters in the strings.
     *
     * Example:
     * <code>
     * $equal = Strings::equalsIgnoreCase('ABC123', 'abc123');
     * </code>
     *
     * @param $string1
     * @param $string2
     * @return bool
     */
    public static function equalsIgnoreCase($string1, $string2)
    {
        return strtolower($string1) == strtolower($string2);
    }

    /**
     * Removes all occurrences of a substring from string.
     *
     * Example:
     * <code>
     * $string = 'winter is coming???!!!';
     * $result = Strings::remove($string, '???');
     * </code>
     *
     * @param $string
     * @param $stringToRemove
     * @return mixed
     */
    public static function remove($string, $stringToRemove)
    {
        return $string && $stringToRemove ? str_replace($stringToRemove, '', $string) : $string;
    }

    /**
     * Adds suffix to the string.
     *
     * Example:
     * <code>
     * $string = 'Daenerys';
     * $stringWithSuffix = Strings::appendSuffix($string, ' Targaryen');
     * </code>
     *
     * @param $string
     * @param string $suffix
     * @return string
     */
    public static function appendSuffix($string, $suffix = '')
    {
        return $string ? $string . $suffix : $string;
    }

    /**
     * Converts a word into the format for an Ouzo table name. Converts 'ModelName' to 'model_names'.
     *
     * Example:
     * <code>
     * $class = "BigFoot";
     * $table = Strings::tableize($class); //BigFeet
     * </code>
     *
     * @param string $class The class names to tableize.
     * @return string The tableized word.
     */
    public static function tableize($class)
    {
        $underscored = Strings::camelCaseToUnderscore($class);
        $parts = explode('_', $underscored);
        $suffix = Inflector::pluralize(array_pop($parts));
        $parts[] = $suffix;
        return implode('_', $parts);
    }

    /**
     * Changes new lines to &lt;br&gt; and converts special characters to HTML entities.
     *
     * Example:
     * <code>
     * $string = "My name is <strong>Reek</strong> \nit rhymes with leek";
     * $escaped = Strings::escapeNewLines($string);
     * </code>
     *
     * @param $string
     * @return string
     */
    public static function escapeNewLines($string)
    {
        $string = htmlspecialchars($string);
        return nl2br($string);
    }
}