<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Http\HttpMethod;
use Ouzo\Http\HttpStatus;
use Ouzo\Routing\Annotation\Route;
use Ouzo\Routing\Annotation\Route\Put;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PutTest extends TestCase
{
    #[Test]
    public function shouldExtendRouteAnnotationClass()
    {
        //then
        $this->assertInstanceOf(Route::class, new Put(''));
    }

    #[DataProvider('getValidParameters')]
    public function testRouteParameters(string $path, ?int $httpResponseCode): void
    {
        //when
        $route = new Put($path, $httpResponseCode);

        //then
        $this->assertEquals([HttpMethod::PUT], $route->getHttpMethods());
        $this->assertEquals($path, $route->getPath());
        $this->assertEquals($httpResponseCode, $route->getHttpResponseCode());
    }

    public static function getValidParameters(): array
    {
        return [
            ['/foo', null],
            ['/foo', HttpStatus::OK],
        ];
    }
}
