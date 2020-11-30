<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Routing\Route;
use Ouzo\Routing\GroupedRoute;
use Ouzo\Tests\Assert;

use PHPUnit\Framework\TestCase;

class GroupedRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
        GroupedRoute::setGroupName('api');
        Route::$isDebug = false;
    }

    /**
     * @test
     */
    public function shouldAddGetRoute()
    {
        //given
        GroupedRoute::get('/user/index', 'GroupedRouteController', 'index');
        GroupedRoute::get('/user/show/id/:id', 'GroupedRouteController', 'show');

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
        GroupedRoute::post('/user/save', 'GroupedRouteController', 'save');
        GroupedRoute::post('/user/update/id/:id', 'GroupedRouteController', 'update');

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
        GroupedRoute::any('/user/save', 'GroupedRouteController', 'save');
        GroupedRoute::any('/user/update/id/:id', 'GroupedRouteController', 'update');

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
        GroupedRoute::any('/user/delete/:id', 'GroupedRouteController', 'delete');

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
        GroupedRoute::resource('GroupedRouteController', 'users');

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
