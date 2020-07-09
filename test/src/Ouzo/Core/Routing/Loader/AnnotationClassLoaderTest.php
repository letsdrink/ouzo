<?php

use Application\Model\Test\CrudController;
use Application\Model\Test\FooClass;
use Application\Model\Test\GlobalController;
use Application\Model\Test\MultipleMethods;
use Application\Model\Test\SimpleController;
use Doctrine\Common\Annotations\AnnotationReader;
use Ouzo\Routing\Loader\AnnotationClassLoader;
use Ouzo\Routing\Loader\RouteMetadata;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;

class AnnotationClassLoaderTest extends TestCase
{
    private $loader;

    public function setUp(): void
    {
        parent::setUp();
        $reader = new AnnotationReader();
        $this->loader = new AnnotationClassLoader($reader);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenClassNonExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->loader->load(['ClassThatDoesNotExist']);
    }

    /**
     * @test
     */
    public function shouldNotLoadAnyRouteMetadata()
    {
        $routes = $this->loader->load([FooClass::class]);

        $this->assertEquals(0, $routes->count());
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadata()
    {
        $routes = $this->loader->load([SimpleController::class]);

        $this->assertEquals(1, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/action', 'GET', SimpleController::class, 'action')
        );
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadataFromSingleMethod()
    {
        $routes = $this->loader->load([MultipleMethods::class]);

        $this->assertEquals(2, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/get', 'GET', MultipleMethods::class, 'getAndPost'),
            new RouteMetadata('/post', 'POST', MultipleMethods::class, 'getAndPost')
        );
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadataFromManyMethods()
    {
        $routes = $this->loader->load([CrudController::class]);

        $this->assertEquals(4, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/create', 'POST', CrudController::class, 'post'),
            new RouteMetadata('/read', 'GET', CrudController::class, 'get'),
            new RouteMetadata('/update', 'PUT', CrudController::class, 'put'),
            new RouteMetadata('/delete', 'DELETE', CrudController::class, 'delete')
        );
    }

    /**
     * @test
     */
    public function shouldLoadRouteMetadataWithGlobalUriPrefix()
    {
        $routes = $this->loader->load([GlobalController::class]);

        $this->assertEquals(2, $routes->count());
        Assert::thatArray($routes->toArray())->containsExactly(
            new RouteMetadata('/prefix/', 'GET', GlobalController::class, 'index'),
            new RouteMetadata('/prefix/action', 'POST', GlobalController::class, 'action')
        );
    }
}