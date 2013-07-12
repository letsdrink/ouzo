<?php
namespace Thulium;

use Thulium\Config\CustomConfig;

class Config
{
    static protected $_config = array();
    static private $_configInstance;

    /**
     * @var CustomConfig
     */
    private static $_customConfigs = array();

    private function __construct()
    {
        if (empty(self::$_config)) {
            self::$_config = $this->_loadConfig();
        }
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
            $classPath = $config->getPath();
            if (file_exists($classPath)) {
                include_once $classPath;
            }
            $className = $config->getConfig();
            $configClass = new $className();
            $customConfigs = array_replace_recursive($customConfigs, $configClass->getConfig());
        }
        return $customConfigs;
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

    /**
     * @param CustomConfig $customConfig
     * @return Config
     */
    public static function registerConfig(CustomConfig $customConfig)
    {
        self::$_customConfigs[] = $customConfig;
        if (empty(self::$_config)) {
            return self::load();
        } else {
            return self::$_configInstance->_addConfig();
        }
    }

    private function _addConfig()
    {
        self::$_config = $this->_loadConfig();
        return self::$_configInstance;
    }
}