<?php

use Ouzo\Routing\Annotation\Route;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    /**
     * @test
     */
    public function shouldExtendRouteAnnotationClass()
    {
        $this->assertInstanceOf(Route::class, new Route\Post([]));
    }

    /**
     * @test
     * @dataProvider getValidParameters
     */
    public function testRouteParameters($parameter, $value, $getter, $result)
    {
        $route = new Route\Post([$parameter => $value]);
        $this->assertEquals($route->$getter(), $result);
    }

    public function getValidParameters()
    {
        return [
            ['value', '/foo', 'getPath', '/foo'],
            ['path', '/bar', 'getPath', '/bar'],
            ['methods', ['GET', 'POST'], 'getMethods', ['POST']],
        ];
    }
}