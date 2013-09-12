<?php
namespace Ouzo\Utilities;

class Path
{
    public static function join()
    {
        $args = FluentArray::from(func_get_args())->filter(Functions::notBlank())->toArray();
        return preg_replace('~[/\\\]+~', DIRECTORY_SEPARATOR, implode(DIRECTORY_SEPARATOR, $args));
    }

    public static function joinWithTemp()
    {
        $args = array_merge(array(sys_get_temp_dir()), func_get_args());
        return call_user_func_array('\Ouzo\Utilities\Path::join', $args);
    }
}