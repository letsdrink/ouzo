<?php

use Ouzo\Routing\Annotation\Route;
use PHPUnit\Framework\TestCase;

class GetTest extends TestCase
{
    /**
     * @test
     */
    public function shouldExtendRouteAnnotationClass()
    {
        $this->assertInstanceOf(Route::class, new Route\Get([]));
    }

    /**
     * @test
     * @dataProvider getValidParameters
     */
    public function testRouteParameters($parameter, $value, $getter, $result)
    {
        $route = new Route\Get([$parameter => $value]);
        $this->assertEquals($route->$getter(), $result);
    }

    public function getValidParameters()
    {
        return [
            ['value', '/foo', 'getPath', '/foo'],
            ['path', '/bar', 'getPath', '/bar'],
            ['methods', ['GET', 'POST'], 'getMethods', ['GET']],
        ];
    }
}