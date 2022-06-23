<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Http\HttpMethod;
use Ouzo\Routing\Annotation\Route;
use PHPUnit\Framework\TestCase;

class RouteAnnotationTest extends TestCase
{
    public function tearDown(): void
    {
        Config::revertProperty('sample', 'route', 'path');

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReadPathFromConfig(): void
    {
        //given
        Config::overrideProperty('sample', 'route', 'path')->with('/api/sample/path');

        $route = new Route('${sample.route.path}', [HttpMethod::DELETE], null);

        //when
        $path = $route->getPath();

        //then
        $this->assertEquals('/api/sample/path', $path);
    }

    /**
     * @test
     */
    public function shouldReturnConfigKeyWhenConfigValueIsNotSet(): void
    {
        //given
        $route = new Route('${sample.route.path}', [HttpMethod::DELETE], null);

        //when
        $path = $route->getPath();

        //then
        $this->assertEquals('${sample.route.path}', $path);
    }
}
