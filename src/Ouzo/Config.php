<?php
namespace Ouzo;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

class Config
{
    private $_config = array();
    private static $_configInstance;
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
        $configPath = Path::join(ROOT_PATH, 'config', getenv('environment'), 'ConfigPanel.php');
        if (file_exists($configPath)) {
            /** @noinspection PhpIncludeInspection */
            return require($configPath);
        }
        return array();
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

    /**
     * Sample usage:
     *  getValue('system_name') - will return $config['system_name']
     *  getValue('db', 'host') - will return $config['db']['host']
     *
     * If value does not exist it will return empty array.
     *
     * @return mixed
     */
    public static function getValue()
    {
        $configValue = self::getInstance()->_config;
        $args = func_get_args();
        foreach ($args as $arg) {
            $configValue = Arrays::getValue($configValue, $arg);
            if (!$configValue) {
                return null;
            }
        }
        return $configValue;
    }

    private static function getInstance()
    {
        if (!self::isLoaded()) {
            self::$_configInstance = new self();
        }
        return self::$_configInstance;
    }

    public static function getPrefixSystem()
    {
        return self::getInstance()->_config['global']['prefix_system'];
    }

    public static function all()
    {
        return self::getInstance()->_config;
    }

    /**
     * @return Config
     */
    public static function registerConfig($customConfig)
    {
        self::$_customConfigs[] = $customConfig;
        if (!self::isLoaded()) {
            self::getInstance();
        } else {
            self::$_configInstance->_reload();
        }
        return self::$_configInstance;
    }

    public static function overrideProperty()
    {
        return new ConfigOverrideProperty(func_get_args());
    }

    public static function overridePropertyArray($keys, $value)
    {
        self::getInstance()->_overrideProperty($keys, $value);
    }

    private function _overrideProperty($keys, $value)
    {
        $keys = Arrays::toArray($keys);
        $config = & $this->_config;
        foreach ($keys as $key) {
            $config = & $config[$key];
        }
        $config = $value;
    }

    public static function clearProperty()
    {
        self::overridePropertyArray(func_get_args(), null);
    }
}

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