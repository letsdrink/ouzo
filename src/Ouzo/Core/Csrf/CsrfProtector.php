<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Csrf;

use Ouzo\Exception\ForbiddenException;
use Ouzo\Controller;
use Ouzo\ExceptionHandling\Error;
use Ouzo\I18n;
use Ouzo\Request\RequestHeaders;
use Ouzo\Session;
use Ouzo\Uri;
use Ouzo\Utilities\Arrays;

class CsrfProtector
{
    public static function protect(Controller $controller)
    {
        $controller->before[] = function () {
            if (CsrfProtector::isMethodProtected(Uri::getRequestType())) {
                CsrfProtector::validate();
            }
            return true;
        };
        $controller->after[] = function () use ($controller) {
            $controller->setCookie([
                'name' => 'csrftoken',
                'value' => CsrfProtector::getCsrfToken(),
                'expire' => 0,
                'path' => '/'
            ]);
            return true;
        };
    }

    public static function validate()
    {
        $csrfToken = self::getCsrfToken();
        if (!isset($_COOKIE['csrftoken']) || $_COOKIE['csrftoken'] != $csrfToken) {
            self::_throwException();
        }

        $headerToken = Arrays::getValue(RequestHeaders::all(), 'X-Csrftoken');
        $postToken = Arrays::getValue($_POST, 'csrftoken');

        if ($headerToken != $csrfToken && $postToken != $csrfToken) {
            self::_throwException();
        }
    }

    public static function isMethodProtected($method)
    {
        return !in_array($method, ['GET', 'HEAD', 'OPTIONS', 'TRACE']);
    }

    public static function getCsrfToken()
    {
        if (Session::has('csrftoken')) {
            return Session::get('csrftoken');
        }
        $token = self::generateCsrfToken();
        Session::set('csrftoken', $token);
        return $token;
    }

    private static function generateCsrfToken()
    {
        $length = 32;
        $bytes = openssl_random_pseudo_bytes(($length + 1) / 2);
        $hex = bin2hex($bytes);
        return substr(base64_encode($hex), 0, $length);
    }

    private static function _throwException()
    {
        throw new ForbiddenException(new Error(defined('UNAUTHORIZED') ? UNAUTHORIZED : 0, I18n::t('exception.forbidden')));
    }
}
