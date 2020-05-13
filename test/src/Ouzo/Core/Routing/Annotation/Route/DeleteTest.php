<?php

use Ouzo\Routing\Annotation\Route;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    /**
     * @test
     */
    public function shouldExtendRouteAnnotationClass()
    {
        $this->assertInstanceOf(Route::class, new Route\Delete([]));
    }

    /**
     * @test
     * @dataProvider getValidParameters
     */
    public function testRouteParameters($parameter, $value, $getter, $result)
    {
        $route = new Route\Delete([$parameter => $value]);
        $this->assertEquals($route->$getter(), $result);
    }

    public function getValidParameters()
    {
        return [
            ['value', '/foo', 'getPath', '/foo'],
            ['path', '/bar', 'getPath', '/bar'],
            ['methods', ['GET', 'POST'], 'getMethods', ['DELETE']],
        ];
    }
}