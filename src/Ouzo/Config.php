<?php
namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Path;

class Config
{
    private $_config = array();
    private static $_configInstance;
    private static $_customConfigs = array();
    private $_overriddenConfig = array();

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
        $configSession = $this->_getConfigSession();
        return array_replace_recursive($configEnv, $configCustom, $configSession);
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

    private function _getConfigSession()
    {
        $session = isset($_SESSION) ? $_SESSION : array();
        return Arrays::getValue($session, 'config', array());
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

    public static function overridePropertyArray($keys, $value, $revert = false)
    {
        self::getInstance()->_overrideProperty($keys, $value, $revert);
    }

    private function _overrideProperty($keys, $value)
    {
        $keys = Arrays::toArray($keys);
        $config = & $this->_config;
        $overriddenConfig = & $this->_overriddenConfig;
        foreach ($keys as $key) {
            $config = & $config[$key];
            $overriddenConfig[$key] = array();
            $overriddenConfig = & $overriddenConfig[$key];
        }
        $overriddenConfig = $config;
        $config = $value;
    }

    private function _revertProperty($keys)
    {
        $keys = Arrays::toArray($keys);
        $config = & $this->_config;
        $overriddenConfig = & $this->_overriddenConfig;
        foreach ($keys as $key) {
            if (!isset($overriddenConfig[$key])) {
                throw new InvalidArgumentException('Cannot revert. No configuration override for: ' . Objects::toString($keys));
            }
            $config = & $config[$key];
            $overriddenConfig = & $overriddenConfig[$key];
        }
        $config = $overriddenConfig;
    }

    public static function clearProperty()
    {
        self::overridePropertyArray(func_get_args(), null);
    }

    public static function revertProperty()
    {
        self::revertPropertyArray(func_get_args());
    }

    private static function revertPropertyArray($keys)
    {
        self::getInstance()->_revertProperty($keys);
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