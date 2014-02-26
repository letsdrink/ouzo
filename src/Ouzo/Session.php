<?php
namespace Ouzo;

class Session
{

    public static function create()
    {
        return new SessionObject();
    }

    public static function startSession()
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            self::_setSavePath();
            if (session_id() == '') {
                session_start();
            }
        }
    }

    private static function _setSavePath()
    {
        $path = Config::getValue('session', 'path');
        if ($path) {
            if (!is_dir($path)) {
                mkdir($path, 0700, true);
            }
            session_save_path($path);
        }
    }

    public static function has($key)
    {
        return self::create()->has($key);
    }

    public static function get($key)
    {
        return self::create()->get($key);
    }

    public static function set($key, $value)
    {
        return self::create()->set($key, $value);
    }

    public static function flush()
    {
        return self::create()->flush();
    }

    public static function remove($key)
    {
        return self::create()->remove($key);
    }

    public static function all()
    {
        return self::create()->all();
    }

    public static function push($key, $value)
    {
        return self::create()->push($key, $value);
    }
}