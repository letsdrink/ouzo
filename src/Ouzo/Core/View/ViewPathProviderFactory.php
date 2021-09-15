<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Ouzo\Config;

class ViewPathProviderFactory
{
    public static function create(): ViewPathProvider
    {
        $provider = Config::getValue('view', 'path_provider') ?? DefaultViewPathProvider::class;
        return new $provider();
    }
}