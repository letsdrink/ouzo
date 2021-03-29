<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use InvalidArgumentException;
use Ouzo\Config\ConfigOverrideProperty;
use Ouzo\Config\ConfigRepository;
use ReflectionMethod;

class Config
{
    private static ?ConfigRepository $configInstance = null;

    public static function isLoaded(): bool
    {
        return self::$configInstance ? true : false;
    }

    /**
     * Sample usage:
     *  getValue('system_name') - will return $config['system_name']
     *  getValue('db', 'host') - will return $config['db']['host']
     *
     * If value does not exist it will return empty array.
     */
    public static function getValue(?string ...$keys): mixed
    {
        return self::getInstance()->getValue($keys);
    }

    private static function getInstance(): ConfigRepository
    {
        if (!self::isLoaded()) {
            self::$configInstance = new ConfigRepository();
            self::$configInstance->reload();
        }
        return self::$configInstance;
    }

    private static function getInstanceNoReload(): ConfigRepository
    {
        if (!self::isLoaded()) {
            self::$configInstance = new ConfigRepository();
        }
        return self::$configInstance;
    }

    public static function getPrefixSystem(): string
    {
        return self::getValue('global', 'prefix_system');
    }

    public static function all(): array
    {
        return self::getInstance()->all();
    }

    public static function registerConfig(object|string $customConfig): ConfigRepository
    {
        if (!is_object($customConfig)) {
            throw new InvalidArgumentException('Custom config must be a object');
        }
        if (!method_exists($customConfig, 'getConfig')) {
            throw new InvalidArgumentException('Custom config object must have getConfig method');
        }

        $reflection = new ReflectionMethod($customConfig, 'getConfig');
        if (!$reflection->isPublic()) {
            throw new InvalidArgumentException('Custom config method getConfig must be public');
        }

        $config = self::getInstanceNoReload();
        $config->addCustomConfig($customConfig);
        return self::$configInstance;
    }

    public static function overrideProperty(string ...$keys): ConfigOverrideProperty
    {
        return new ConfigOverrideProperty($keys);
    }

    public static function clearProperty(string ...$keys): void
    {
        self::overridePropertyArray($keys, null);
    }

    public static function revertProperty(string ...$keys): void
    {
        self::revertPropertyArray($keys);
    }

    /** @param string[] $keys */
    public static function revertPropertyArray(array $keys): void
    {
        self::getInstance()->revertProperty($keys);
    }

    /** @param string[] $keys */
    public static function overridePropertyArray(array $keys, mixed $value): void
    {
        self::getInstance()->overrideProperty($keys, $value);
    }
}
