<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

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
    public static function create(): SessionObject
    {
        return new SessionObject();
    }

    public static function isStarted(): bool
    {
        return isset($_SESSION);
    }

    public static function startSession(): void
    {
        if (version_compare(phpversion(), '5.4.0', '>=') && \PHP_SESSION_ACTIVE === session_status()) {
            throw new RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (version_compare(phpversion(), '5.4.0', '<') && isset($_SESSION) && session_id()) {
            throw new RuntimeException('Failed to start the session: already started by PHP ($_SESSION is set).');
        }

        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            throw new RuntimeException("Failed to start the session: headers already sent by \"{$file}\" at line {$line}.");
        }

        self::setSavePath();
        if (!session_start()) {
            throw new RuntimeException('Failed to start the session');
        }
    }

    private static function setSavePath(): void
    {
        $path = Config::getValue('session', 'path');
        if ($path) {
            if (!is_dir($path)) {
                mkdir($path, 0700, true);
            }
            session_save_path($path);
        }
    }

    public static function has(string...$keys): bool
    {
        return self::create()->has(...$keys);
    }

    public static function get(string...$keys): mixed
    {
        return self::create()->get(...$keys);
    }

    public static function set(mixed...$keys): SessionObject
    {
        return self::create()->set(...$keys);
    }

    public static function flush(): SessionObject
    {
        return self::create()->flush();
    }

    public static function remove(string...$keys): void
    {
        self::create()->remove(...$keys);
    }

    public static function all(): ?array
    {
        return self::create()->all();
    }

    public static function push(mixed...$args): void
    {
        self::create()->push(...$args);
    }
}
