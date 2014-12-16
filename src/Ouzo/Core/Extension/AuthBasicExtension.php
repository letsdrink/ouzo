<?php

namespace Ouzo\Extension;

use Ouzo\Api\UnauthorizedException;
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
 * AuthBasicExtension::register($this, array(
 *  'login' => $login,
 *  'password' => $password,
 *  'realm' => $realm
 * ));
 * </pre>
 */
class AuthBasicExtension
{
    public static function register($controller, $params)
    {
        $authUser = $params['login'];
        $authPassword = $params['password'];
        $realm = Arrays::getValue($params, 'realm', 'Ouzo Auth');

        $controller->before[] = function () use ($authUser, $authPassword, $realm) {
            return AuthBasicExtension::_checkCredentials($authUser, $authPassword, $realm);
        };
    }

    public static function _checkCredentials($authUser, $authPassword, $realm)
    {
        $login = Arrays::getValue($_SERVER, 'PHP_AUTH_USER');
        $pass = Arrays::getValue($_SERVER, 'PHP_AUTH_PW');
        if ($authUser != $login || $authPassword != $pass) {
            $code = defined('UNAUTHORIZED') ? UNAUTHORIZED : 0;
            $error = new Error($code, I18n::t('exception.unauthorized'));
            throw new UnauthorizedException($error, array('WWW-Authenticate: Basic realm="' . $realm . '"'));
        }
        return true;
    }
}
