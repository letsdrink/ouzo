<?php
namespace Ouzo\Uri;

use Ouzo\Utilities\Arrays;

class PathProvider
{
    public function getPath()
    {
        return Arrays::getValue($_SERVER, 'REQUEST_URI', '/');
    }
}