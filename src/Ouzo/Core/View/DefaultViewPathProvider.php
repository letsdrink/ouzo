<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Ouzo\ApplicationPaths;
use Ouzo\Utilities\Path;

class DefaultViewPathProvider implements ViewPathProvider
{
    function getViewPath(string $viewName, string $extension): string
    {
        return Path::join(ROOT_PATH, ApplicationPaths::getViewPath(), $viewName . $extension);
    }
}