<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\CrudController;
use Application\Model\Test\FooClass;
use Application\Model\Test\GlobalController;
use Application\Model\Test\MultipleMethods;
use Application\Model\Test\SimpleController;
use Ouzo\Routing\Loader\AnnotationClassLoader;
use Ouzo\Routing\Loader\RouteMetadata;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;

class AnnotationClassLoaderTest extends TestCase
{
    private AnnotationClassLoader $loader;

    public function setUp(): void
    {
        parent::setUp();
        $this->loader = new AnnotationClassLoader();
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenClassNonExists()
    {
        //then
        $this->expectException(InvalidArgumentException::class);

        //when
        $this->loader->load(['ClassThatDoesNotExist']);
    }

    /**
     * @test
     */
    public function shouldNotLoadAnyRouteMetadata()
    {
        //when
        $routes = $this->loader->load([FooClass::class]);

        //then
        $this->assertEquals(0, $routes->count());
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadata()
    {
        //when
        $routes = $this->loader->load([SimpleController::class]);

        //then
        $this->assertEquals(1, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/action', 'GET', SimpleController::class, 'action', null)
        );
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadataFromSingleMethod()
    {
        //when
        $routes = $this->loader->load([MultipleMethods::class]);

        //then
        $this->assertEquals(2, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/get', 'GET', MultipleMethods::class, 'getAndPost', null),
            new RouteMetadata('/post', 'POST', MultipleMethods::class, 'getAndPost', null)
        );
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadataFromManyMethods()
    {
        //when
        $routes = $this->loader->load([CrudController::class]);

        //then
        $this->assertEquals(4, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/create', 'POST', CrudController::class, 'post', null),
            new RouteMetadata('/read', 'GET', CrudController::class, 'get', null),
            new RouteMetadata('/update', 'PUT', CrudController::class, 'put', null),
            new RouteMetadata('/delete', 'DELETE', CrudController::class, 'delete', null)
        );
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadataWithGlobalUriPrefix()
    {
        //when
        $routes = $this->loader->load([GlobalController::class]);

        //then
        $this->assertEquals(2, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/prefix/', 'GET', GlobalController::class, 'index', null),
            new RouteMetadata('/prefix/action', 'POST', GlobalController::class, 'action', null)
        );
    }
}
