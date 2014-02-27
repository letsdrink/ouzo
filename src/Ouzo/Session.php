<?php
namespace Ouzo;

use RuntimeException;

/**
 * Class Session
 * @package Ouzo\Utilities
 *
 * Session is facade for session handling. Session data is stored in files. Path can be set in configuration if you
 * want to change your system's default.
 *
 * All session handling methods (except of all and flush) supports nested keys e.g.
 * <code>
 *  Session::set('key1', 'key2', 'value');
 *  Session::get('key1', 'key2', 'value');
 *  Session::push('key1', 'key2', 'value');
 *  Session::has('key1', 'key2');
 *  Session::remove('key1', 'key2');
 * </code>
 */
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

    public static function has()
    {
        return self::create()->has(func_get_args());
    }

    public static function get()
    {
        return self::create()->get(func_get_args());
    }

    public static function set()
    {
        return self::create()->set(func_get_args());
    }

    public static function flush()
    {
        return self::create()->flush();
    }

    public static function remove()
    {
        return self::create()->remove(func_get_args());
    }

    public static function all()
    {
        return self::create()->all();
    }

    public static function push()
    {
        return self::create()->push(func_get_args());
    }
}