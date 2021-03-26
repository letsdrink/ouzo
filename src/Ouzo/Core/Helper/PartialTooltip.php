<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Helper;

use Ouzo\Config;
use Ouzo\Utilities\Strings;

class PartialTooltip
{
    public static function wrap(?string $content, string $viewName): string
    {
        if (self::shouldWrap($viewName)) {
            return self::tooltipStart($viewName) . $content . self::tooltipEnd($viewName);
        }
        return $content;
    }

    private static function tooltipStart(string $viewName): string
    {
        return '<!-- [PARTIAL] ' . $viewName . ' -->';
    }

    private static function tooltipEnd(string $viewName): string
    {
        return '<!-- [END PARTIAL] ' . $viewName . ' -->';
    }

    private static function shouldWrap(string $viewName): bool
    {
        return Config::getValue('debug') && !self::isJavaScriptView($viewName);
    }

    private static function isJavaScriptView(string $viewName): bool
    {
        return Strings::endsWith($viewName, '.js') || Strings::endsWith($viewName, '_js');
    }
}
