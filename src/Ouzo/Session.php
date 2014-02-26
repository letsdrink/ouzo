<?php
namespace Ouzo;

use RuntimeException;

class Session
{

    public static function create()
    {
        return new SessionObject();
    }

    public static function startSession()
    {
        if (version_compare(phpversion(), '5.4.0', '>=') && \PHP_SESSION_ACTIVE === session_status()) {
            throw new RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (version_compare(phpversion(), '5.4.0', '<') && isset($_SESSION) && session_id()) {
            throw new RuntimeException('Failed to start the session: already started by PHP ($_SESSION is set).');
        }

        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            throw new RuntimeException(sprintf('Failed to start the session: headers already sent by "%s" at line %d.', $file, $line));
        }

        self::_setSavePath();
        if (!session_start()) {
            throw new RuntimeException('Failed to start the session');
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