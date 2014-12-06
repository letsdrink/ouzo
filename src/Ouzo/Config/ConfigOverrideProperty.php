<?php
namespace Ouzo\Config;

use Ouzo\Config;

class ConfigOverrideProperty
{
    private $keys;

    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    public function with($value)
    {
        Config::overridePropertyArray($this->keys, $value);
    }
}
