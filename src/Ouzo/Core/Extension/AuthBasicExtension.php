<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Extension;

use Ouzo\Controller;
use Ouzo\Exception\UnauthorizedException;
use Ouzo\ExceptionHandling\Error;
use Ouzo\I18n;
use Ouzo\Utilities\Arrays;

/**
 * Class AuthBasicExtension
 * @package Ouzo\Extension
 * <br/>
 * Usage: <br/>
 *
 * In your controller's init method add:
 * <pre>
 * AuthBasicExtension::register($this, [
 *  'login' => $login,
 *  'password' => $password,
 *  'realm' => $realm
 * ]);
 * </pre>
 */
class AuthBasicExtension
{
    public static function register(Controller $controller, array $params): void
    {
        $authUser = $params['login'];
        $authPassword = $params['password'];
        $realm = Arrays::getValue($params, 'realm', 'Ouzo Auth');

        $controller->before[] = function () use ($authUser, $authPassword, $realm) {
            return AuthBasicExtension::checkCredentials($authUser, $authPassword, $realm);
        };
    }

    public static function checkCredentials(string $authUser, string $authPassword, string $realm): bool
    {
        $login = Arrays::getValue($_SERVER, 'PHP_AUTH_USER');
        $pass = Arrays::getValue($_SERVER, 'PHP_AUTH_PW');
        if ($authUser != $login || $authPassword != $pass) {
            $code = defined('UNAUTHORIZED') ? UNAUTHORIZED : 0;
            $error = new Error($code, I18n::t('exception.unauthorized'));
            throw new UnauthorizedException($error, ['WWW-Authenticate: Basic realm="' . $realm . '"']);
        }
        return true;
    }
}
