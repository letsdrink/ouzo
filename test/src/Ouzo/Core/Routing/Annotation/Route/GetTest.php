<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Http\HttpMethod;
use Ouzo\Http\HttpStatus;
use Ouzo\Routing\Annotation\Route;
use Ouzo\Routing\Annotation\Route\Get;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    /**
     * @test
     */
    public function shouldExtendRouteAnnotationClass()
    {
        //then
        $this->assertInstanceOf(Route::class, new Get(''));
    }

    /**
     * @test
     * @dataProvider getValidParameters
     */
    public function testRouteParameters(string $path, ?int $httpResponseCode)
    {
        //when
        $route = new Get($path, $httpResponseCode);

        //then
        $this->assertEquals([HttpMethod::GET], $route->getHttpMethods());
        $this->assertEquals($path, $route->getPath());
        $this->assertEquals($httpResponseCode, $route->getHttpResponseCode());
    }

    public function getValidParameters(): array
    {
        return [
            ['/foo', null],
            ['/foo', HttpStatus::OK],
        ];
    }
}
