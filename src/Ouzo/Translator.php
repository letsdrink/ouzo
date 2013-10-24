<?php
namespace Ouzo;

class Translator
{
    private $_labels;

    function __construct($labels)
    {
        $this->_labels = $labels;
    }

    public function translate($key, $params = array())
    {
        $explodedKey = explode('.', $key);
        $translation = $this->_getAt($this->_labels, $explodedKey, $key);
        return $this->sprintf_assoc($translation, $params);
    }

    private function _getAt(array $array, array $indices, $default)
    {
        foreach ($indices as $index) {
            if (!isset($array[$index])) {
                return $default;
            }
            $array = $array[$index];
        }
        return $array;
    }

    private function sprintf_assoc($string, $params)
    {
        foreach ($params as $k => $v) {
            $string = preg_replace("/%{($k)}/", $v, $string);
        }
        return $string;
    }
}