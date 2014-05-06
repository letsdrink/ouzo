<?php
namespace Ouzo\Uri;

use Ouzo\Utilities\Arrays;

class PathProvider
{
    public function getPath()
    {
        $uri = Arrays::getValue($_SERVER, 'REDIRECT_URL');
        if (!$uri) {
            return Arrays::getValue($_SERVER, 'REQUEST_URI', '/');
        }
        $queryString = Arrays::getValue($_SERVER, 'REDIRECT_QUERY_STRING');
        return $queryString ? $uri . '?' . $queryString : $uri;
    }
}