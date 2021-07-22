<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Ouzo\Config;

class ViewNameProviderFactory
{
    public static function create(): ViewNameProvider
    {
        $provider = Config::getValue("view", "name_provider") ?? DefaultViewNameProvider::class;
        return new $provider();
    }
}