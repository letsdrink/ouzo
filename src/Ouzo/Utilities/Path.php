<?php
namespace Ouzo\Utilities;

class Path
{
    public static function join()
    {
        $args = Arrays::filterNotBlank(func_get_args());
        return preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $args));
    }

    public static function joinWithTemp()
    {
        $args = array_merge(array(sys_get_temp_dir()), func_get_args());
        return call_user_func_array('\Ouzo\Utilities\Path::join', $args);
    }

    public static function normalize($path)
    {
        $parts = explode('/', trim($path, '/'));
        $result = array();
        foreach ($parts as $part) {
            if ($part == '..' && !empty($result)) {
                array_pop($result);
            } else if ($part != '.' && !empty($part)) {
                array_push($result, $part);
            }
        }
        $root = $path[0] == '/' ? '/' : '';
        return $root . implode('/', $result);
    }
}