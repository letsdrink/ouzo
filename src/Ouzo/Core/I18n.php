<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Exception;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Files;
use Ouzo\Utilities\Path;

class I18n
{
    const DEFAULT_LANGUAGE = 'en';

    private static ?Translator $translator = null;
    private static ?array $labels = [];

    public static function t(string $key, array $params = [], ?PluralizeOption $pluralize = null): string
    {
        if (!$key) {
            return '';
        }
        if (!self::$translator) {
            self::$translator = self::getTranslator();
        }
        if ($pluralize != null) {
            return self::$translator->translateWithChoice($key, $pluralize->getValue(), $params);
        }
        return self::$translator->translate($key, $params);
    }

    public static function reset(Translator $translator = null): void
    {
        self::$translator = $translator;
        self::$labels = null;
    }

    public static function labels(string $key = ''): ?array
    {
        $labels = self::loadLabels();
        $explodedKey = explode('.', $key);
        return $key ? Arrays::getNestedValue($labels, $explodedKey) : $labels;
    }

    public static function loadLabels(): array
    {
        if (!self::$labels) {
            $language = self::getLanguage();
            $path = Path::join(ROOT_PATH, 'locales', $language . '.php');
            if (!Files::exists($path)) {
                throw new Exception('Cannot find declared language file: ' . $language);
            }
            /** @noinspection PhpIncludeInspection */
            self::$labels = require($path);
        }
        return self::$labels;
    }

    private static function getTranslator(): Translator
    {
        $labels = self::loadLabels();
        return new Translator(self::getLanguage(), $labels);
    }

    private static function getLanguage(): string
    {
        return Config::getValue('language') ?: I18n::DEFAULT_LANGUAGE;
    }

    public static function pluralizeBasedOn(int $value): PluralizeOption
    {
        return new PluralizeOption($value);
    }
}
