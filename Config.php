<?php
namespace Thulium;

class Config
{
    static protected $_config = array();

    static private $_configInstance;

    private function __construct()
    {
        if (empty(self::$_config)) {
            self::$_config = $this->_loadConfig();
        }
    }

    private function _loadConfig()
    {
        $configEnv = $this->_getConfigEnv();
        $configGlobal = $this->_getConfigGlobal();
        return array_replace_recursive($configEnv, $configGlobal);
    }

    private function _getConfigEnv()
    {
        $configEnv = array();
        $configPath = ROOT_PATH . 'config/' . getenv('environment') . '/ConfigPanel.php';
        if (file_exists($configPath)) {
            $configEnv = include($configPath);
        }
        return $configEnv;
    }

    private function _getConfigGlobal()
    {
        $configGlobal = array();
        $configPath = '/etc/thulium/ConfigPanel.php';
        if (file_exists($configPath)) {
            $configGlobal = include($configPath);
        }
        return $configGlobal;
    }

    public function getConfig($section)
    {
        return (!empty(self::$_config[$section]) ? self::$_config[$section] : array());
    }

    public function getAllConfig()
    {
        return (!empty(self::$_config) ? self::$_config : array());
    }

    static public function load()
    {
        if (!self::$_configInstance) {
            self::$_configInstance = new self();
        }
        return self::$_configInstance;
    }

    public static function getPrefixSystem()
    {
        self::load();
        return self::$_config['global']['prefix_system'];
    }
}