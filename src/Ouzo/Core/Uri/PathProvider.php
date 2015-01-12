<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
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
