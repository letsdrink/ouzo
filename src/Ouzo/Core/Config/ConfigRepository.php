<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Config;

use InvalidArgumentException;
use Ouzo\Session;
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
        return Session::get('config') ? : array();
    }

    public function overrideProperty($keys, $value)
    {
        $keys = Arrays::toArray($keys);
        $oldValue = Arrays::getNestedValue($this->_config, $keys);
        Arrays::setNestedValue($this->_config, $keys, $value);
        Arrays::setNestedValue($this->_overriddenConfig, $keys, $oldValue);
    }

    public function revertProperty($keys)
    {
        $keys = Arrays::toArray($keys);
        $config = & $this->_config;
        $overriddenConfig = & $this->_overriddenConfig;
        $overriddenKey = null;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $overriddenConfig)) {
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
        return Arrays::getNestedValue($this->_config, $args);
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
