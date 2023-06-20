<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Uri\PathProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PathProviderTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        unset($_SERVER['REDIRECT_URL']);
        unset($_SERVER['REDIRECT_QUERY_STRING']);
        unset($_SERVER['REQUEST_URI']);
    }

    #[Test]
    public function shouldReturnRedirectUrlIfExists()
    {
        // given
        $provider = new PathProvider();
        $_SERVER['REDIRECT_URL'] = '/redirect/url';

        // when
        $path = $provider->getPath();

        // then
        $this->assertEquals('/redirect/url', $path);
    }

    #[Test]
    public function shouldReturnRedirectUrlWithRedirectQueryStringIfExists()
    {
        // given
        $provider = new PathProvider();
        $_SERVER['REDIRECT_URL'] = '/redirect/url';
        $_SERVER['REDIRECT_QUERY_STRING'] = 'id=1&name=john';

        // when
        $path = $provider->getPath();

        // then
        $this->assertEquals('/redirect/url?id=1&name=john', $path);
    }

    #[Test]
    public function shouldReturnRequestUriIfRedirectUrlNotExist()
    {
        // given
        $provider = new PathProvider();
        $_SERVER['REQUEST_URI'] = '/request/uri';

        // when
        $path = $provider->getPath();

        // then
        $this->assertEquals('/request/uri', $path);
    }
}
