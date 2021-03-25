<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\View;

use Ouzo\Config;

class ViewRendererFactory
{
    public static function create(string $viewName, array $attributes): ViewRenderer
    {
        $rendererClass = Config::getValue('renderer', $viewName);
        if ($rendererClass) {
            return new $rendererClass($viewName, $attributes);
        }
        $rendererClass = Config::getValue('renderer', 'default');
        if ($rendererClass) {
            return new $rendererClass($viewName, $attributes);
        }
        return new PhtmlRenderer($viewName, $attributes);
    }
}
