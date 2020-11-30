<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Routing\GroupedRoute;
use Ouzo\Routing\Route;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
        Route::$isDebug = false;
    }

    /**
     * @test
     */
    public function shouldAddGetRoute()
    {
        //given
        Route::get('/user/index', 'Controller\\UsersController', 'index');
        Route::get('/user/show/id/:id', 'Controller\\UsersController', 'show');

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
        Route::get('/user/index', 'Controller\\UsersController', 'index');

        //when
        $route = Arrays::first(Route::getRoutes());

        //then
        $this->assertEquals('/user/index', $route->getUri());
        $this->assertEquals('Controller\\UsersController', $route->getController());
        $this->assertEquals('index', $route->getAction());
    }

    /**
     * @test
     */
    public function shouldAddPostRoute()
    {
        //given
        Route::post('/user/save', 'Controller\\UsersController', 'save');
        Route::post('/user/update/id/:id', 'Controller\\UsersController', 'update');

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
        Route::any('/user/save', 'Controller\\UsersController', 'save');
        Route::any('/user/update/id/:id', 'Controller\\UsersController', 'update');

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
        Route::any('/user/save', 'Controller\\UsersController', 'save');
        Route::any('/user/update/id/:id', 'Controller\\UsersController', 'update');
        Route::any('/photo/index', 'Controller\\Admin\\UsersController', 'index');

        //when
        $controllerRoutes = Route::getRoutesForController('Controller\\UsersController');

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
        Route::any('/user/save', 'Controller\\UsersController', 'save');
        Route::any('/user/update/id/:id', 'Controller\\UsersController', 'update');

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
        Route::get('/user/save', 'Controller\\UsersController', 'save_one');

        //when
        try {
            Route::get('/user/save', 'Controller\\UsersController', 'save_two');
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
        Route::get('/user/save', 'Controller\\UsersController', 'save');
        Route::post('/user/save', 'Controller\\UsersController', 'save');

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
        Route::resource('Controller\\UsersController', 'users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)->hasSize(8);
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfNoActionInGetMethod()
    {
        //when
        CatchException::when(new Route())->get('/user/save', 'Controller\\UsersController', null);
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfNoActionInPostMethod()
    {
        //when
        CatchException::when(new Route())->post('/user/save', 'Controller\\UsersController', null);
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfNoActionInAnyMethod()
    {
        //when
        CatchException::when(new Route())->any('/user/save', 'Controller\\UsersController', null);
        CatchException::assertThat()->isInstanceOf(InvalidArgumentException::class);
    }

    /**
     * @test
     */
    public function shouldRouteForAllowingAllActionsInController()
    {
        //given
        Route::allowAll('/users', 'Controller\\UsersController');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(1, $routes);
        $this->assertEquals('Controller\\UsersController', $routes[0]->getController());
        $this->assertNull($routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldNotValidateExistingRoutes()
    {
        //given
        Route::$validate = false;
        Route::get('/users/index', 'Controller\\UsersController', 'index');
        Route::get('/users/index', 'Controller\\UsersController', 'index');
        Route::$validate = true;

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)->hasSize(2);
    }

    /**
     * @test
     */
    public function shouldSetRuleNameToGetMethod()
    {
        //given
        Route::get('/users/index', 'Controller\\UsersController', 'index');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('indexUsersPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetCustomRuleNameToGetMethod()
    {
        //given
        Route::get('/users/index', 'Controller\\UsersController', 'index', ['as' => 'all_users']);

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('allUsersPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetRuleNameToPostMethod()
    {
        //given
        Route::post('/users/save', 'Controller\\UsersController', 'save');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('saveUsersPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetCustomRuleNameToPostMethod()
    {
        //given
        Route::post('/users/save', 'Controller\\UsersController', 'save', ['as' => 'add_user']);

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('addUserPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetRuleNameToAnyMethod()
    {
        //given
        Route::any('/users/add', 'Controller\\UsersController', 'add');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('addUsersPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetCustomRuleNameToAnyMethod()
    {
        //given
        Route::any('/users/add', 'Controller\\UsersController', 'add', ['as' => 'create_user']);

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('createUserPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldNotSetRuleNameToAllowAllMethod()
    {
        //given
        Route::allowAll('/users', 'Controller\\UsersController');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEmpty($routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldNotSetCustomRuleNameToAllowAllMethod()
    {
        //given
        Route::allowAll('/users', 'Controller\\UsersController', ['as' => 'custom']);

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEmpty($routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetRulesNameToResourceMethod()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getName')
            ->contains('usersPath', 'freshUserPath', 'editUserPath', 'userPath');
    }

    /**
     * @test
     */
    public function shouldSetRuleNameForMultipartControllerNames()
    {
        //given
        Route::resource('Controller\\BigFeetController', 'big_feet');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getName')
            ->contains('bigFeetPath', 'freshBigFootPath', 'editBigFootPath', 'bigFootPath');
    }

    /**
     * @test
     */
    public function shouldSetRuleNameForMultipartCamelcaseAction()
    {
        //given
        Route::post('/users/save', 'Controller\\UsersController', 'saveMyUser');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('saveMyUserUsersPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldGenerateNameForNestedRoute()
    {
        //given
        Route::post('/users/save', 'Controller\\Admin\\UsersController', 'saveMyUser');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('saveMyUserUsersAdminPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldGenerateNameForNestedResourceRoute()
    {
        //given
        Route::resource('Controller\\Admin\\UsersController', 'admin/users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getName')
            ->contains('usersAdminPath', 'freshUserAdminPath', 'editUserAdminPath', 'userAdminPath');
    }

    /**
     * @test
     */
    public function shouldAddPutRoute()
    {
        //given
        Route::put('/users/save', 'Controller\\UsersController', 'save');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('Controller\\UsersController', $routes[0]->getController());
        $this->assertEquals('save', $routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldAddDeleteRoute()
    {
        //given
        Route::delete('/users/:id/delete', 'Controller\\UsersController', 'delete');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('Controller\\UsersController', $routes[0]->getController());
        $this->assertEquals('delete', $routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldAddRouteInGroup()
    {
        //given
        Route::group('api', function () {
            GroupedRoute::post('/users/:id/archive', 'Controller\\UsersController', 'archive');
        });

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(1, $routes);
        $this->assertEquals('/api/users/:id/archive', $routes[0]->getUri());
        $this->assertEquals('archive', $routes[0]->getAction());
        $this->assertEquals('Controller\\UsersController', $routes[0]->getController());
        $this->assertEquals('POST', $routes[0]->getMethod());
    }

    /**
     * @test
     */
    public function shouldAddAllowAll()
    {
        //given
        Route::get('/user', 'Controller\\UsersController', 'index');
        Route::allowAll('/user', 'Controller\\UsersController');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(2, $routes);
    }

    /**
     * @test
     */
    public function shouldAddOptions()
    {
        //given
        Route::options('/user', 'Controller\\UsersController', 'options');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(1, $routes);
        $this->assertEquals('OPTIONS', $routes[0]->getMethod());
    }
}
