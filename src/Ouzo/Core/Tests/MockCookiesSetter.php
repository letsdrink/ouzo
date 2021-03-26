<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Tests;

use Ouzo\CookiesSetter;

class MockCookiesSetter extends CookiesSetter
{
    private array $cookies = [];

    public function setCookies(array $cookies): void
    {
        $this->cookies = $cookies;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }
}
