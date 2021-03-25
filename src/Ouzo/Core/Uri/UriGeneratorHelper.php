<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Uri;

use Ouzo\Config;
use Ouzo\Routing\RouteRule;

class UriGeneratorHelper
{
    public static function getApplicationPrefix(RouteRule $routeRule): string
    {
        $method = $routeRule->getMethod();
        $applicationPrefixForGet = Config::getValue('global', 'prefix_system_get');
        if ($method === 'GET' && !empty($applicationPrefixForGet)) {
            return $applicationPrefixForGet;
        }
        return Config::getValue('global', 'prefix_system');
    }
}
