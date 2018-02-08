<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Uri\PathProvider;

use PHPUnit\Framework\TestCase; 

class PathProviderTest extends TestCase
{
    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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
