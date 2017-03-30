<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Uri;

use Ouzo\Config;
use Ouzo\Routing\RouteRule;

class UriGeneratorHelper
{
    public static function getApplicationPrefix(RouteRule $routeRule)
    {
        $method = $routeRule->getMethod();
        $applicationPrefixForGet = Config::getValue("global", "prefix_system_get");
        if ($method == 'GET' && $applicationPrefixForGet) {
            return $applicationPrefixForGet;
        }
        return Config::getValue("global", "prefix_system");
    }
}
