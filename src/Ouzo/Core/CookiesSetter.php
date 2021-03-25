<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo;

use Ouzo\Utilities\Arrays;

class CookiesSetter
{
    public function setCookies(array $cookies): void
    {
        Arrays::each($cookies, fn(array $cookies) => setcookie(
            $cookies['name'],
            $cookies['value'],
            Arrays::getValue($cookies, 'expire'),
            Arrays::getValue($cookies, 'path'),
            Arrays::getValue($cookies, 'domain'),
            Arrays::getValue($cookies, 'secure'),
            Arrays::getValue($cookies, 'httponly')
        )
        );
    }
}
