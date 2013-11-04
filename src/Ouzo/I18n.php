<?php
namespace Ouzo;

use Exception;
use Ouzo\Utilities\Path;

class I18n
{
    const DEFAULT_LANGUAGE = 'en';
    private static $_translator;

    public static function t($key, $params = array())
    {
        if (!$key) {
            return '';
        }
        if (!self::$_translator) {
            self::$_translator = self::_getTranslator();
        }
        return self::$_translator->translate($key, $params);
    }

    public static function reset()
    {
        self::$_translator = null;
    }

    private static function _loadLabels()
    {
        $language = Config::getValue('language') ? : I18n::DEFAULT_LANGUAGE;
        $path = Path::join(ROOT_PATH, 'locales', $language . '.php');
        if (!file_exists($path)) {
            throw new Exception('Cannot find declared language file: ' . $language);
        }
        /** @noinspection PhpIncludeInspection */
        return require($path);
    }

    private static function _getTranslator()
    {
        $_labels = self::_loadLabels();
        return new Translator($_labels);
    }
}