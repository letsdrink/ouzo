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
    private array $customConfigs = [];
    private array $config = [];
    private array $overriddenConfig = [];

    public function reload(): void
    {
        $this->config = $this->load();
    }

    public function load(): array
    {
        $configEnv = $this->getConfigEnv();
        $defaultConfigEnv = $this->getDefaultConfigEnv();
        $configCustom = $this->getConfigCustom();
        $configSession = $this->getConfigFromSession();
        return array_replace_recursive($defaultConfigEnv, $configEnv, $configCustom, $configSession);
    }

    private function getConfigEnv(): array
    {
        $configPath = Path::join(ROOT_PATH, 'config', getenv('environment'), 'config.php');
        return $this->getConfigEnvFromPath($configPath);
    }

    private function getDefaultConfigEnv(): array
    {
        $configPath = Path::join(ROOT_PATH, 'config', 'config.php');
        return $this->getConfigEnvFromPath($configPath);
    }

    private function getConfigCustom(): array
    {
        $result = [];
        foreach ($this->customConfigs as $config) {
            $result = array_replace_recursive($result, $config->getConfig());
        }
        return $result;
    }

    private function getConfigFromSession(): array
    {
        return Session::get('config') ?: [];
    }

    /** @var string[] $keys */
    public function overrideProperty(array $keys, mixed $value): void
    {
        $keys = Arrays::toArray($keys);
        $oldValue = Arrays::getNestedValue($this->config, $keys);
        Arrays::setNestedValue($this->config, $keys, $value);
        Arrays::setNestedValue($this->overriddenConfig, $keys, $oldValue);
    }

    /** @var string[] $keys */
    public function revertProperty(array $keys): void
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

    /** @var string[] $args */
    public function getValue(array $args): mixed
    {
        return Arrays::getNestedValue($this->config, $args);
    }

    public function all(): array
    {
        return $this->config;
    }

    public function addCustomConfig(object $customConfig): void
    {
        $this->customConfigs[] = $customConfig;
    }

    private function getConfigEnvFromPath(string $configPath): array
    {
        if (file_exists($configPath)) {
            /** @noinspection PhpIncludeInspection */
            return require($configPath);
        }
        return [];
    }
}
