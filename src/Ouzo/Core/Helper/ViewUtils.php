<?php

namespace Ouzo\Helper;


class ViewUtils
{
    static function fileIncludeTag($type, $url)
    {
        switch ($type) {
            case 'link':
                return '<link rel="stylesheet" href="' . $url . '" type="text/css" />' . PHP_EOL;
            case 'script':
                return '<script type="text/javascript" src="' . $url . '"></script>' . PHP_EOL;
        }
        return null;
    }
}