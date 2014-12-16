<?php
namespace Ouzo;

use Ouzo\Config\ConfigOverrideProperty;
use Ouzo\Config\ConfigRepository;

class Config
{
    private static $_configInstance;

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
        return self::getInstance()->getValue(func_get_args());
    }

    private static function getInstance()
    {
        if (!self::isLoaded()) {
            self::$_configInstance = new ConfigRepository();
            self::$_configInstance->reload();
        }
        return self::$_configInstance;
    }

    private static function getInstanceNoReload()
    {
        if (!self::isLoaded()) {
            self::$_configInstance = new ConfigRepository();
        }
        return self::$_configInstance;
    }

    public static function getPrefixSystem()
    {
        return self::getValue('global', 'prefix_system');
    }

    public static function all()
    {
        return self::getInstance()->all();
    }

    /**
     * @param $customConfig
     * @return Config
     */
    public static function registerConfig($customConfig)
    {
        $config = self::getInstanceNoReload();
        $config->addCustomConfig($customConfig);
        $config->reload();
        return self::$_configInstance;
    }

    public static function overrideProperty()
    {
        return new ConfigOverrideProperty(func_get_args());
    }

    public static function clearProperty()
    {
        self::overridePropertyArray(func_get_args(), null);
    }

    public static function revertProperty()
    {
        self::revertPropertyArray(func_get_args());
    }

    public static function revertPropertyArray($keys)
    {
        self::getInstance()->revertProperty($keys);
    }

    public static function overridePropertyArray($keys, $value, $revert = false)
    {
        self::getInstance()->overrideProperty($keys, $value, $revert);
    }
}
