<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo;

use Ouzo\Config\ConfigOverrideProperty;
use Ouzo\Config\ConfigRepository;

class Config
{
    /**
     * @var null|ConfigRepository
     */
    private static $configInstance = null;

    /**
     * @return null|ConfigRepository
     */
    public static function isLoaded()
    {
        return self::$configInstance;
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
        return self::getInstance()->getValue(func_get_args());
    }

    /**
     * @return ConfigRepository
     */
    private static function getInstance()
    {
        if (!self::isLoaded()) {
            self::$configInstance = new ConfigRepository();
            self::$configInstance->reload();
        }
        return self::$configInstance;
    }

    /**
     * @return ConfigRepository
     */
    private static function getInstanceNoReload()
    {
        if (!self::isLoaded()) {
            self::$configInstance = new ConfigRepository();
        }
        return self::$configInstance;
    }

    /**
     * @return string
     */
    public static function getPrefixSystem()
    {
        return self::getValue('global', 'prefix_system');
    }

    /**
     * @return array
     */
    public static function all()
    {
        return self::getInstance()->all();
    }

    /**
     * @param $customConfig
     * @return ConfigRepository
     */
    public static function registerConfig($customConfig)
    {
        $config = self::getInstanceNoReload();
        $config->addCustomConfig($customConfig);
        return self::$configInstance;
    }

    /**
     * @return ConfigOverrideProperty
     */
    public static function overrideProperty()
    {
        return new ConfigOverrideProperty(func_get_args());
    }

    /**
     * @return void
     */
    public static function clearProperty()
    {
        self::overridePropertyArray(func_get_args(), null);
    }

    /**
     * @return void
     */
    public static function revertProperty()
    {
        self::revertPropertyArray(func_get_args());
    }

    /**
     * @param $keys
     * @return void
     */
    public static function revertPropertyArray($keys)
    {
        self::getInstance()->revertProperty($keys);
    }

    /**
     * @param $keys
     * @param $value
     * @return void
     */
    public static function overridePropertyArray($keys, $value)
    {
        self::getInstance()->overrideProperty($keys, $value);
    }
}
