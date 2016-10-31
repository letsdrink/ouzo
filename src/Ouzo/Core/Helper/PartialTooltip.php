<?php
namespace Ouzo\Core\Helper;


use Ouzo\Config;
use Ouzo\Utilities\Strings;

class PartialTooltip
{
    public static function wrap($content, $viewName)
    {
        if (self::shouldWrap($viewName)) {
            return self::tooltipStart($viewName) . $content . self::tooltipEnd($viewName);
        }
        return $content;
    }

    private static function tooltipStart($viewName)
    {
        return '<!-- [PARTIAL] ' . $viewName . ' -->';
    }

    private static function tooltipEnd($viewName)
    {
        return '<!-- [END PARTIAL] ' . $viewName . ' -->';
    }

    private static function shouldWrap($viewName)
    {
        return Config::getValue('debug') && !self::isJavaScriptView($viewName);
    }

    private static function isJavaScriptView($viewName)
    {
        return Strings::endsWith('.js', $viewName) || Strings::endsWith('_js', $viewName);
    }
}
