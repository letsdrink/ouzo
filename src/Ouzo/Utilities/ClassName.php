<?php
namespace Ouzo\Utilities;

class ClassName
{
    public static function pathToFullyQualifiedName($string)
    {
        $parts = explode('/', $string);
        $namespace = '';
        foreach ($parts as $part) {
            $namespace .= Strings::underscoreToCamelCase($part) . '\\';
        }
        return rtrim($namespace, '\\');
    }
}