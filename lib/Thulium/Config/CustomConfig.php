<?php
namespace Thulium\Config;

class CustomConfig
{
    private $_config;
    private $_path;

    public function __construct($config, $path = '')
    {
        $this->_config = $config;
        $this->_path = $path;
    }

    public function getConfig()
    {
        return is_array($this->_config) ? $this->_config['config'] : $this->_config;
    }

    public function getPath()
    {
        return is_array($this->_config) ? $this->_config['path'] : $this->_path;
    }
}