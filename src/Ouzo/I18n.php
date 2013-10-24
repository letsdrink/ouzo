<?php
namespace Ouzo;

use Pl;

class I18n
{
    private static $_translator;

    public static function t($key, $params = array())
    {
        if (!$key) {
            return '';
        }
        if (!self::$_translator) {
            $_labels = Pl::getLabels();
            self::$_translator = new Translator($_labels);
        }
        return self::$_translator->translate($key, $params);
    }
}