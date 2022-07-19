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
    private const VALUE_SEPARATOR = ':';

    public static function selectConfigValue(string $selector): mixed
    {
        if (Strings::startsWith($selector, self::CONFIG_START) && Strings::endsWith($selector, self::CONFIG_END)) {
            $selector = Strings::removePrefix($selector, self::CONFIG_START);
            $selector = Strings::removeSuffix($selector, self::CONFIG_END);
            [$selector, $defaultValue] = self::extractDefaultValue($selector);

            $arguments = explode('.', $selector);
            return Config::getValue(...$arguments) ?? $defaultValue;
        }

        return $selector;
    }

    private static function extractDefaultValue(string $selector): array
    {
        $parts = explode(self::VALUE_SEPARATOR, $selector, 2);
        $defaultValue = null;
        if (sizeof($parts) === 2) {
            $selector = $parts[0];
            $defaultValue = $parts[1];
        }
        return [$selector, $defaultValue];
    }
}
