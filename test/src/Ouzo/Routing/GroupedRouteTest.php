<?php

use Ouzo\Routing\Route;
use Ouzo\Routing\GroupedRoute;
use Ouzo\Tests\Assert;

class GroupedRouteTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        parent::setUp();
        Route::$routes = array();
        GroupedRoute::setGroupName('api');
    }

    /**
     * @test
     */
    public function shouldAddGetRoute()
    {
        //given
        GroupedRoute::get('/user/index', 'User#index');
        GroupedRoute::get('/user/show/id/:id', 'User#show');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getUri')
            ->containsOnly('/api/user/index', '/api/user/show/id/:id');
    }

    /**
     * @test
     */
    public function shouldAddPostRoute()
    {
        //given
        GroupedRoute::post('/user/save', 'User#save');
        GroupedRoute::post('/user/update/id/:id', 'User#update');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getUri')
            ->containsOnly('/api/user/save', '/api/user/update/id/:id');
    }

    /**
     * @test
     */
    public function shouldAddAnyRoute()
    {
        //given
        GroupedRoute::any('/user/save', 'User#save');
        GroupedRoute::any('/user/update/id/:id', 'User#update');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getUri')
            ->containsOnly('/api/user/save', '/api/user/update/id/:id');
    }

    /**
     * @test
     */
    public function shouldAddDeleteRoute()
    {
        //given
        GroupedRoute::any('/user/delete/:id', 'User#delete');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getUri')
            ->containsOnly('/api/user/delete/:id');
    }

    /**
     * @test
     */
    public function shouldCreateRouteForResource()
    {
        //given
        GroupedRoute::resource('users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getUri')
            ->containsOnly(
                '/api/users',
                '/api/users/fresh',
                '/api/users/:id/edit',
                '/api/users/:id',
                '/api/users',
                '/api/users/:id',
                '/api/users/:id',
                '/api/users/:id');
    }

} 