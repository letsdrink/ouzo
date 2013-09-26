<?php

use Ouzo\Routing\Route;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Arrays;

class RouteTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        Route::$routes = array();
    }

    /**
     * @test
     */
    public function shouldAddGetRoute()
    {
        //given
        Route::get('/user/index', 'User#index');
        Route::get('/user/show/id/:id', 'User#show');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(2, $routes);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectRouteRule()
    {
        //given
        Route::get('/user/index', 'User#index');

        //when
        $route = Arrays::first(Route::getRoutes());

        //then
        $this->assertEquals('/user/index', $route->getUri());
        $this->assertEquals('User', $route->getController());
        $this->assertEquals('index', $route->getAction());
    }

    /**
     * @test
     */
    public function shouldAddPostRoute()
    {
        //given
        Route::post('/user/save', 'User#save');
        Route::post('/user/update/id/:id', 'User#update');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(2, $routes);
    }

    /**
     * @test
     */
    public function shouldAddAnyRoute()
    {
        //given
        Route::any('/user/save', 'User#save');
        Route::any('/user/update/id/:id', 'User#update');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(2, $routes);
    }

    /**
     * @test
     */
    public function shouldSearchRouteForController()
    {
        //given
        Route::any('/user/save', 'User#save');
        Route::any('/user/update/id/:id', 'User#update');
        Route::any('/photo/index', 'Photo#index');

        //when
        $controllerRoutes = Route::getRoutesForController('user');

        //then
        $this->assertCount(2, $controllerRoutes);
        $this->assertCount(3, Route::getRoutes());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayIfNoRoutesForController()
    {
        //given
        Route::any('/user/save', 'User#save');
        Route::any('/user/update/id/:id', 'User#update');

        //when
        $controllerRoutes = Route::getRoutesForController('photo');

        //then
        $this->assertEmpty($controllerRoutes);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionForDuplicatedRules()
    {
        //given
        Route::get('/user/save', 'User#save_one');

        //when
        try {
            Route::get('/user/save', 'User#save_two');
            $this->fail();
        } catch (InvalidArgumentException $exception) {
        }

        //then
        $routes = Route::getRoutes();
        $this->assertCount(1, $routes);
        $this->assertEquals('save_one', $routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldDefineMultipleRulesWithDifferentTypes()
    {
        //given
        Route::get('/user/save', 'User#save');
        Route::post('/user/save', 'User#save');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(2, $routes);
    }

    /**
     * @test
     */
    public function shouldCreateRouteForResource()
    {
        //given
        Route::resource('users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)->hasSize(8);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoActionInGetMethod()
    {
        //when
        try {
            Route::get('/user/save', 'User');
            $this->fail();
        } catch (InvalidArgumentException $exception) {
        }
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoActionInPostMethod()
    {
        //when
        try {
            Route::post('/user/save', 'User');
            $this->fail();
        } catch (InvalidArgumentException $exception) {
        }
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoActionInAnyMethod()
    {
        //when
        try {
            Route::any('/user/save', 'User');
            $this->fail();
        } catch (InvalidArgumentException $exception) {
        }
    }
}