<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;

class Config
{
    private $_config = array();
    static private $_configInstance;
    private static $_customConfigs = array();

    private function __construct()
    {
        $this->_reload();
    }

    private function _reload()
    {
        $this->_config = $this->_loadConfig();
    }

    private function _loadConfig()
    {
        $configEnv = $this->_getConfigEnv();
        $configCustom = $this->_getConfigCustom();
        return array_replace_recursive($configEnv, $configCustom);
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

    private function _getConfigCustom()
    {
        $customConfigs = array();
        foreach (self::$_customConfigs as $config) {
            $customConfigs = array_replace_recursive($customConfigs, $config->getConfig());
        }
        return $customConfigs;
    }

    public static function isLoaded()
    {
        return self::$_configInstance;
    }

    public static function getValue()
    {
        return call_user_func_array(array(self::load(), 'getConfig'), func_get_args());
    }

    public function getConfig()
    {
        $configValue = $this->_config;
        $args = func_get_args();
        foreach ($args as $arg) {
            $configValue = Arrays::getValue($configValue, $arg, array());
        }
        return $configValue;
    }

    public function getAllConfig()
    {
        return $this->_config;
    }

    static public function load()
    {
        if (!self::isLoaded()) {
            self::$_configInstance = new self();
        }
        return self::$_configInstance;
    }

    public static function getPrefixSystem()
    {
        return self::load()->_config['global']['prefix_system'];
    }

    /**
     * @return Config
     */
    public static function registerConfig($customConfig)
    {
        self::$_customConfigs[] = $customConfig;
        if (!self::isLoaded()) {
            self::load();
        } else {
            self::$_configInstance->_reload();
        }
        return self::$_configInstance;
    }

}