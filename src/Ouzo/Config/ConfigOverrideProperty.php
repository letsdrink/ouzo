<?php
namespace Ouzo\Config;

use Ouzo\Config;

class ConfigOverrideProperty
{
    private $keys;

    function __construct($keys)
    {
        $this->keys = $keys;
    }

    function with($value)
    {
        Config::overridePropertyArray($this->keys, $value);
    }
}