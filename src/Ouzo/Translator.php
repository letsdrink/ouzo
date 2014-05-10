<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class Translator
{
    private $_labels;

    public function __construct($labels)
    {
        $this->_labels = $labels;
    }

    public function translate($key, $params = array())
    {
        $explodedKey = explode('.', $key);
        $translation = Arrays::getNestedValue($this->_labels, $explodedKey) ? : $key;
        return $this->sprintf_assoc($translation, $params);
    }

    private function sprintf_assoc($string, $params)
    {
        foreach ($params as $k => $v) {
            $string = preg_replace("/%{($k)}/", $v, $string);
        }
        return $string;
    }
}