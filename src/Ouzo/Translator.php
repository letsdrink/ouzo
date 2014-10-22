<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

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
        $translation = Arrays::getNestedValue($this->_labels, $explodedKey) ?: $key;
        return Strings::sprintAssoc($translation, $params);
    }
}