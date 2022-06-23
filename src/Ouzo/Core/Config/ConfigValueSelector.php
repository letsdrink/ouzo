<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Config;

use Ouzo\Config;
use Ouzo\Utilities\Strings;

class ConfigValueSelector
{
    private const CONFIG_START = '${';
    private const CONFIG_END = '}';

    public static function selectConfigValue(string $selector): mixed
    {
        if (Strings::startsWith($selector, self::CONFIG_START) && Strings::endsWith($selector, self::CONFIG_END)) {
            $selector = Strings::removePrefix($selector, self::CONFIG_START);
            $selector = Strings::removeSuffix($selector, self::CONFIG_END);
            $arguments = explode('.', $selector);
            return Config::getValue(...$arguments);
        }

        return null;
    }
}
