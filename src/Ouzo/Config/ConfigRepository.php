<?php
namespace Ouzo\Config;

use InvalidArgumentException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Objects;
use Ouzo\Utilities\Path;

class ConfigRepository
{
    private $_customConfigs = array();
    private $_config = array();
    private $_overriddenConfig = array();

    public function reload()
    {
        $this->_config = $this->load();
    }

    public function load()
    {
        $configEnv = $this->_getConfigEnv();
        $configCustom = $this->_getConfigCustom();
        $configSession = $this->_getConfigFromSession();
        return array_replace_recursive($configEnv, $configCustom, $configSession);
    }

    private function _getConfigEnv()
    {
        $configPath = Path::join(ROOT_PATH, 'config', getenv('environment'), 'config.php');
        if (file_exists($configPath)) {
            /** @noinspection PhpIncludeInspection */
            return require($configPath);
        }
        return array();
    }

    private function _getConfigCustom()
    {
        $result = array();
        foreach ($this->_customConfigs as $config) {
            $result = array_replace_recursive($result, $config->getConfig());
        }
        return $result;
    }

    private function _getConfigFromSession()
    {
        return isset($_SESSION) ? Arrays::getValue($_SESSION, 'config', array()) : array();
    }

    public function overrideProperty($keys, $value)
    {
        $keys = Arrays::toArray($keys);
        $config = & $this->_config;
        $overriddenConfig = & $this->_overriddenConfig;
        foreach ($keys as $key) {
            $config = & $config[$key];
            if (!isset($overriddenConfig[$key])) {
                $overriddenConfig[$key] = array();
            }
            $overriddenConfig = & $overriddenConfig[$key];
        }
        $overriddenConfig = $config;
        $config = $value;
    }

    public function revertProperty($keys)
    {
        $keys = Arrays::toArray($keys);
        $config = & $this->_config;
        $overriddenConfig = & $this->_overriddenConfig;
        $overriddenKey = null;
        foreach ($keys as $key) {
            if (!isset($overriddenConfig[$key])) {
                throw new InvalidArgumentException('Cannot revert. No configuration override for: ' . Objects::toString($keys));
            }
            $config = & $config[$key];
            if (is_array($overriddenConfig[$key])) {
                $overriddenConfig = & $overriddenConfig[$key];
            } else {
                $overriddenKey = $key;
            }
        }
        $config = $overriddenConfig[$overriddenKey];
    }

    public function getValue($args)
    {
        $configValue = $this->_config;
        foreach ($args as $arg) {
            $configValue = Arrays::getValue($configValue, $arg);
            if (!$configValue) {
                return null;
            }
        }
        return $configValue;
    }

    public function all()
    {
        return $this->_config;
    }

    public function addCustomConfig($customConfig)
    {
        $this->_customConfigs[] = $customConfig;
    }
}