<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Controller;
use Ouzo\Routing\GroupedRoute;
use Ouzo\Routing\Route;
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\TestCase;

class UsersMockController extends Controller
{
    public function save_one()
    {

    }

    public function save_two()
    {

    }
}

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
        Route::get('/user/index', UsersMockController::class, 'index');
        Route::get('/user/show/id/:id', UsersMockController::class, 'show');

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
        Route::get('/user/index', UsersMockController::class, 'index');

        //when
        $route = Arrays::first(Route::getRoutes());

        //then
        $this->assertEquals('/user/index', $route->getUri());
        $this->assertEquals(UsersMockController::class, $route->getController());
        $this->assertEquals('index', $route->getAction());
    }

    /**
     * @test
     */
    public function shouldAddPostRoute()
    {
        //given
        Route::post('/user/save', UsersMockController::class, 'save');
        Route::post('/user/update/id/:id', UsersMockController::class, 'update');

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
        Route::any('/user/save', UsersMockController::class, 'save');
        Route::any('/user/update/id/:id', UsersMockController::class, 'update');

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
        Route::any('/user/save', UsersMockController::class, 'save');
        Route::any('/user/update/id/:id', UsersMockController::class, 'update');
        Route::any('/photo/index', 'Controller\\Admin\\UsersController', 'index');

        //when
        $controllerRoutes = Route::getRoutesForController(UsersMockController::class);

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
        Route::any('/user/save', UsersMockController::class, 'save');
        Route::any('/user/update/id/:id', UsersMockController::class, 'update');

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
        Route::$isDebug = true;
        Route::get('/user/save', UsersMockController::class, 'save_one');

        //when
        try {
            Route::get('/user/save', UsersMockController::class, 'save_two');
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
        Route::get('/user/save', UsersMockController::class, 'save');
        Route::post('/user/save', UsersMockController::class, 'save');

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
        Route::resource(UsersMockController::class, 'users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)->hasSize(8);
    }

    /**
     * @test
     */
    public function shouldRouteForAllowingAllActionsInController()
    {
        //given
        Route::allowAll('/users', UsersMockController::class);

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(1, $routes);
        $this->assertEquals(UsersMockController::class, $routes[0]->getController());
        $this->assertNull($routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldNotValidateExistingRoutes()
    {
        //given
        Route::$validate = false;
        Route::get('/users/index', UsersMockController::class, 'index');
        Route::get('/users/index', UsersMockController::class, 'index');
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
        Route::get('/users/index', UsersMockController::class, 'index');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('indexUsersMockPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetCustomRuleNameToGetMethod()
    {
        //given
        Route::get('/users/index', UsersMockController::class, 'index', ['as' => 'all_users']);

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
        Route::post('/users/save', UsersMockController::class, 'save');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('saveUsersMockPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetCustomRuleNameToPostMethod()
    {
        //given
        Route::post('/users/save', UsersMockController::class, 'save', ['as' => 'add_user']);

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
        Route::any('/users/add', UsersMockController::class, 'add');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('addUsersMockPath', $routes[0]->getName());
    }

    /**
     * @test
     */
    public function shouldSetCustomRuleNameToAnyMethod()
    {
        //given
        Route::any('/users/add', UsersMockController::class, 'add', ['as' => 'create_user']);

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
        Route::allowAll('/users', UsersMockController::class);

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
        Route::allowAll('/users', UsersMockController::class, ['as' => 'custom']);

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
        Route::resource(UsersMockController::class, 'users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)
            ->onMethod('getName')
            ->contains('usersMockPath', 'freshUsersMockPath', 'editUsersMockPath', 'usersMockPath');
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
        Route::post('/users/save', UsersMockController::class, 'saveMyUser');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals('saveMyUserUsersMockPath', $routes[0]->getName());
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
        Route::put('/users/save', UsersMockController::class, 'save');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals(UsersMockController::class, $routes[0]->getController());
        $this->assertEquals('save', $routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldAddDeleteRoute()
    {
        //given
        Route::delete('/users/:id/delete', UsersMockController::class, 'delete');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertEquals(UsersMockController::class, $routes[0]->getController());
        $this->assertEquals('delete', $routes[0]->getAction());
    }

    /**
     * @test
     */
    public function shouldAddRouteInGroup()
    {
        //given
        Route::group('api', function () {
            GroupedRoute::post('/users/:id/archive', UsersMockController::class, 'archive');
        });

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(1, $routes);
        $this->assertEquals('/api/users/:id/archive', $routes[0]->getUri());
        $this->assertEquals('archive', $routes[0]->getAction());
        $this->assertEquals(UsersMockController::class, $routes[0]->getController());
        $this->assertEquals('POST', $routes[0]->getMethod());
    }

    /**
     * @test
     */
    public function shouldAddAllowAll()
    {
        //given
        Route::get('/user', UsersMockController::class, 'index');
        Route::allowAll('/user', UsersMockController::class);

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
        Route::options('/user', UsersMockController::class, 'options');

        //when
        $routes = Route::getRoutes();

        //then
        $this->assertCount(1, $routes);
        $this->assertEquals('OPTIONS', $routes[0]->getMethod());
    }

    /**
     * @test
     */
    public function shouldSkipActionValidationForResource()
    {
        //given
        Route::$isDebug = true;
        Route::resource(UsersMockController::class, 'users');

        //when
        $routes = Route::getRoutes();

        //then
        Assert::thatArray($routes)->hasSize(8);
    }
}
