<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

class Translator
{
    private bool $pseudoLocalizationEnabled;

    public function __construct(private string $language, private array $labels)
    {
        $this->pseudoLocalizationEnabled = Config::getValue('pseudo_localization') ? true : false;
    }

    public function translate($key, $params = [])
    {
        $explodedKey = explode('.', $key);
        $value = Arrays::getNestedValue($this->labels, $explodedKey);
        $translation = $value === null ? $key : $value;
        return $this->localize(Strings::sprintAssoc($translation, $params));
    }

    public function translateWithChoice($key, $choice, $params = [])
    {
        $value = $this->translate($key, $params);

        $split = explode('|', $value);
        $index = $this->getIndex($choice);
        if ($index >= sizeof($split)) {
            $index = sizeof($split) - 1;
        }
        return $this->localize($split[$index]);
    }

    private function localize($text)
    {
        return $this->pseudoLocalizationEnabled ? $this->pseudoLocalize($text) : $text;
    }

    private function pseudoLocalize(array|string $text): array|string
    {
        if (is_array($text)) {
            $array = $text;
            foreach ($array as $key => $value) {
                $array[$key] = is_array($value) ? $this->pseudoLocalize($value) : $this->pseudoLocalizeText($value);
            }
            return $array;
        }
        return $this->pseudoLocalizeText($text);
    }

    private function pseudoLocalizeText(string $text): string
    {
        return $this->strtr_utf8($text,
            "abcdefghijklmnoprstuvwyzABCDEFGHIJKLMNOPRSTUVWYZ",
            "ȧƀƈḓḗƒɠħīĵķŀḿƞǿƥřşŧŭṽẇẏzȦƁƇḒḖƑƓĦĪĴĶĿḾȠǾƤŘŞŦŬṼẆẎẐ"
        );
    }

    private function strtr_utf8(string $text, string $from, string $to): string
    {
        $keys = [];
        $values = [];
        preg_match_all('/./u', $from, $keys);
        preg_match_all('/./u', $to, $values);
        $mapping = array_combine($keys[0], $values[0]);
        return strtr($text, $mapping);
    }

    /*
     * The plural rules are derived from code of the Zend Framework (2010-09-25),
     * which is subject to the new BSD license (http://framework.zend.com/license/new-bsd).
     * Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
     *
     * This method was taken from Symfony2 framework. Copyright (c) 2004-2014 Fabien Potencier.
     */
    public function getIndex(int $number): int
    {
        return match ($this->language) {
            'bo', 'dz', 'id', 'ja', 'jv', 'ka', 'km', 'kn', 'ko', 'ms', 'th', 'tr', 'vi', 'zh' => 0,
            'af', 'az', 'bn', 'bg', 'ca', 'da', 'de', 'el', 'en', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fo', 'fur',
            'fy', 'gl', 'gu', 'ha', 'he', 'hu', 'is', 'it', 'ku', 'lb', 'ml', 'mn', 'mr', 'nah', 'nb', 'ne', 'nl',
            'nn', 'no', 'om', 'or', 'pa', 'pap', 'ps', 'pt', 'so', 'sq', 'sv', 'sw', 'ta', 'te', 'tk', 'ur', 'zu' => $number == 1 ? 0 : 1,
            'am', 'bh', 'fil', 'fr', 'gun', 'hi', 'ln', 'mg', 'nso', 'xbr', 'ti', 'wa' => (($number == 0) || ($number == 1)) ? 0 : 1,
            'be', 'bs', 'hr', 'ru', 'sr', 'uk' => (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2),
            'cs', 'sk' => ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2),
            'ga' => ($number == 1) ? 0 : (($number == 2) ? 1 : 2),
            'lt' => (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2),
            'sl' => ($number % 100 == 1) ? 0 : (($number % 100 == 2) ? 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3)),
            'mk' => ($number % 10 == 1) ? 0 : 1,
            'mt' => ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3)),
            'lv' => ($number == 0) ? 0 : ((($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2),
            'pl' => ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2),
            'cy' => ($number == 1) ? 0 : (($number == 2) ? 1 : ((($number == 8) || ($number == 11)) ? 2 : 3)),
            'ro' => ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2),
            'ar' => ($number == 0) ? 0 : (($number == 1) ? 1 : (($number == 2) ? 2 : ((($number % 100 >= 3) && ($number % 100 <= 10)) ? 3 : ((($number % 100 >= 11) && ($number % 100 <= 99)) ? 4 : 5)))),
            default => 0
        };
    }
}
