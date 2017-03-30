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

    /** @var Translator */
    private static $translator;
    /** @var array|null */
    private static $labels;

    /**
     * @param string $key
     * @param array $params
     * @param PluralizeOption|null $pluralize
     * @return string
     */
    public static function t($key, $params = [], PluralizeOption $pluralize = null)
    {
        if (!$key) {
            return '';
        }
        if (!self::$translator) {
            self::$translator = self::_getTranslator();
        }
        if ($pluralize != null) {
            return self::$translator->translateWithChoice($key, $pluralize->getValue(), $params);
        }
        return self::$translator->translate($key, $params);
    }

    /**
     * @param Translator|null $translator
     * @return void
     */
    public static function reset(Translator $translator = null)
    {
        self::$translator = $translator;
        self::$labels = null;
    }

    /**
     * @param string $key
     * @return array
     */
    public static function labels($key = '')
    {
        $labels = self::loadLabels();
        $explodedKey = explode('.', $key);
        return $key ? Arrays::getNestedValue($labels, $explodedKey) : $labels;
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function loadLabels()
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

    /**
     * @return Translator
     */
    private static function _getTranslator()
    {
        $labels = self::loadLabels();
        return new Translator(self::getLanguage(), $labels);
    }

    /**
     * @return string
     */
    private static function getLanguage()
    {
        return Config::getValue('language') ?: I18n::DEFAULT_LANGUAGE;
    }

    /**
     * @param int $value
     * @return PluralizeOption
     */
    public static function pluralizeBasedOn($value)
    {
        return new PluralizeOption($value);
    }
}
