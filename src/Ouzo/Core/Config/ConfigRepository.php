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
    /** @var array */
    private $customConfigs = [];
    /** @var array */
    private $config = [];
    /** @var array */
    private $overriddenConfig = [];

    /**
     * @return void
     */
    public function reload()
    {
        $this->config = $this->load();
    }

    /**
     * @return array
     */
    public function load()
    {
        $configEnv = $this->getConfigEnv();
        $defaultConfigEnv = $this->getDefaultConfigEnv();
        $configCustom = $this->getConfigCustom();
        $configSession = $this->getConfigFromSession();
        return array_replace_recursive($defaultConfigEnv, $configEnv, $configCustom, $configSession);
    }

    /**
     * @return array
     */
    private function getConfigEnv()
    {
        $configPath = Path::join(ROOT_PATH, 'config', getenv('environment'), 'config.php');
        return $this->getConfigEnvFromPath($configPath);
    }

    /**
     * @return array
     */
    private function getDefaultConfigEnv()
    {
        $configPath = Path::join(ROOT_PATH, 'config', 'config.php');
        return $this->getConfigEnvFromPath($configPath);
    }

    /**
     * @return array
     */
    private function getConfigCustom()
    {
        $result = [];
        foreach ($this->customConfigs as $config) {
            $result = array_replace_recursive($result, $config->getConfig());
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getConfigFromSession()
    {
        return Session::get('config') ?: [];
    }

    /**
     * @param array $keys
     * @param mixed $value
     * @return void
     */
    public function overrideProperty($keys, $value)
    {
        $keys = Arrays::toArray($keys);
        $oldValue = Arrays::getNestedValue($this->config, $keys);
        Arrays::setNestedValue($this->config, $keys, $value);
        Arrays::setNestedValue($this->overriddenConfig, $keys, $oldValue);
    }

    /**
     * @param array $keys
     * @return void
     * @throws InvalidArgumentException
     */
    public function revertProperty($keys)
    {
        $keys = Arrays::toArray($keys);
        $config = &$this->config;
        $overriddenConfig = &$this->overriddenConfig;
        $overriddenKey = null;
        foreach ($keys as $key) {
            if (!array_key_exists($key, $overriddenConfig)) {
                throw new InvalidArgumentException('Cannot revert. No configuration override for: ' . Objects::toString($keys));
            }
            $config = &$config[$key];
            if (is_array($overriddenConfig[$key])) {
                $overriddenConfig = &$overriddenConfig[$key];
            } else {
                $overriddenKey = $key;
            }
        }
        $config = $overriddenConfig[$overriddenKey];
    }

    /**
     * @param array $args
     * @return mixed|null
     */
    public function getValue($args)
    {
        return Arrays::getNestedValue($this->config, $args);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->config;
    }

    /**
     * @param object $customConfig
     * @return void
     */
    public function addCustomConfig($customConfig)
    {
        $this->customConfigs[] = $customConfig;
    }

    private function getConfigEnvFromPath($configPath)
    {
        if (file_exists($configPath)) {
            /** @noinspection PhpIncludeInspection */
            return require($configPath);
        }
        return [];
    }
}
