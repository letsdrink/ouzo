<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Uri;

use Ouzo\Utilities\Arrays;

class PathProvider implements PathProviderInterface
{
    public function getPath()
    {
        return Arrays::getValue($_SERVER, 'REQUEST_URI', '/');
    }
}
