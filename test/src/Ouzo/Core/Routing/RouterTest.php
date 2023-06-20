<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Routing\Route;
use Ouzo\Routing\Router;
use Ouzo\Routing\RouterException;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Uri;
use Ouzo\Uri\PathProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Route::clear();
        Route::$isDebug = false;
    }

    #[Test]
    public function shouldFindRouteGet()
    {
        //given
        Route::get('/user/index', 'Controller\\UserController', 'index');
        $router = $this->_createRouter('GET', '/user/index');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/index', $rule->getUri());
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('Controller\\UserController', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
    }

    #[Test]
    public function shouldNotMatchOnlyPrefixForGet()
    {
        //given
        Route::get('/user/:id', 'Controller\\UserController', 'show');
        Route::get('/user/:id/posts', 'Controller\\UserController', 'posts');
        $router = $this->_createRouter('GET', '/user/1/posts');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('posts', $rule->getAction());
    }

    #[Test]
    public function shouldNotMatchSuffix()
    {
        //given
        Route::allowAll('/tickets', 'Controller\\TicketsController');
        Route::get('/user/:id/tickets/all', 'User', 'posts');
        $router = $this->_createRouter('GET', '/user/1/tickets/all');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('posts', $rule->getAction());
        $this->assertEquals('User', $rule->getController());
    }

    #[Test]
    public function shouldNotMatchControllerPrefixForAllowAll()
    {
        //given
        Route::allowAll('/user', 'Controller\\UserController');
        $router = $this->_createRouter('GET', '/userInvalid/index');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    #[Test]
    public function shouldNotFindRouteIfRequestMethodIsInvalid()
    {
        //given
        Route::get('/user/index', 'Controller\\UserController', 'index');
        $router = $this->_createRouter('POST', '/user/index');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    #[Test]
    public function shouldNotFindRouteIfRequestIsDifferentFromDeclaration()
    {
        //given
        Route::get('/user/index', 'Controller\\UserController', 'index');
        $router = $this->_createRouter('GET', '/user/indexxx');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    #[Test]
    public function shouldFindRouteWithPlaceholderValue()
    {
        //given
        Route::get('/user/show/id/:id', 'Controller\\UserController', 'show');
        $router = $this->_createRouter('GET', '/user/show/id/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/show/id/:id', $rule->getUri());
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('Controller\\UserController', $rule->getController());
        $this->assertEquals('show', $rule->getAction());
    }

    #[Test]
    public function shouldNotFindRouteWhenRequestAndDefinitionIsNotEqualWithPlaceholder()
    {
        //given
        Route::get('/user/show/id/:id', 'Controller\\UserController', 'show');
        $router = $this->_createRouter('GET', '/user/show/id/12/surname/smith');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    #[Test]
    public function shouldFindRoutePost()
    {
        //given
        Route::post('/user/save', 'Controller\\UserController', 'save');
        $router = $this->_createRouter('POST', '/user/save');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/save', $rule->getUri());
        $this->assertEquals('POST', $rule->getMethod());
        $this->assertEquals('Controller\\UserController', $rule->getController());
        $this->assertEquals('save', $rule->getAction());
    }

    #[DataProvider('requestMethods')]
    public function shouldFindRouteAny(string $method): void
    {
        //given
        Route::any('/user/save', 'Controller\\UserController', 'save');
        $router = $this->_createRouter($method, '/user/save');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/save', $rule->getUri());
        $this->assertContains($method, $rule->getMethod());
        $this->assertEquals('Controller\\UserController', $rule->getController());
        $this->assertEquals('save', $rule->getAction());
    }

    #[DataProvider('requestRestMethods')]
    public function shouldFindRouteResource(string $method, string $uri): void
    {
        //given
        Route::resource('Controller\\AlbumsController', 'albums');
        $router = $this->_createRouter($method, $uri);

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals($method, $rule->getMethod());
        $this->assertEquals('Controller\\AlbumsController', $rule->getController());
    }

    #[Test]
    public function shouldFindRouteForAllInController()
    {
        //given
        Route::allowAll('/users', 'Controller\\UsersController');
        $router = $this->_createRouter('GET', '/users/select_for_user');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertFalse($rule->isRequiredAction());
        $this->assertEquals('Controller\\UsersController', $rule->getController());
    }

    #[Test]
    public function shouldNotFindRouteWhenExceptAction()
    {
        //given
        Route::allowAll('/users', 'Controller\\UsersController', ['except' => ['add']]);
        $router = $this->_createRouter('GET', '/users/add');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    #[Test]
    public function shouldFindRouteWhenDeclaredAnIsInExcept()
    {
        //given
        Route::get('/users/add', 'Controller\\UsersController', 'add');
        Route::allowAll('/users', 'Controller\\UsersController', ['except' => ['add']]);
        $router = $this->_createRouter('GET', '/users/add');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertNotEmpty($rule);
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('add', $rule->getAction());
        $this->assertEquals('Controller\\UsersController', $rule->getController());
    }

    #[Test]
    public function shouldFindRouteAndGetParamsFromPath()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'Controller\\UsersController', 'show');
        $router = $this->_createRouter('GET', '/users/show/id/1/call_id/2');

        //when
        $rule = $router->findRoute();

        //then
        Assert::thatArray($rule->getParameters())->hasSize(2)->containsKeyAndValue(['id' => 1, 'call_id' => 2]);
    }

    #[Test]
    public function shouldFindRouteRestIndex()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('GET', '/users');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
        $this->assertEmpty($rule->getParameters());
    }

    #[Test]
    public function shouldFindRouteRestNew()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('GET', '/users/fresh');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('fresh', $rule->getAction());
        $this->assertEmpty($rule->getParameters());
    }

    #[Test]
    public function shouldFindRouteRestCreate()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('POST', '/users');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('create', $rule->getAction());
        $this->assertEmpty($rule->getParameters());
    }

    #[Test]
    public function shouldFindRouteRestShow()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('GET', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('show', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    #[Test]
    public function shouldFindRouteRestEdit()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('GET', '/users/12/edit');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('edit', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    #[Test]
    public function shouldFindRouteRestUpdatePut()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('PUT', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('update', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    #[Test]
    public function shouldFindRouteRestUpdatePatch()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('PATCH', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('update', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    #[Test]
    public function shouldFindRouteRestDestroy()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('DELETE', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('destroy', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    #[Test]
    public function shouldFindRouteRuleWithDefaultRoute()
    {
        //given
        Route::resource('Controller\\UsersController', 'users');
        $router = $this->_createRouter('GET', '/users/');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\UsersController', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
    }

    #[Test]
    public function shouldFindRouteWithSpecialCharactersInParameter()
    {
        //given
        Route::get('/resources/:file', 'resources', 'server');
        $router = $this->_createRouter('GET', '/resources/file_name.js');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('resources', $rule->getController());
        $this->assertEquals('server', $rule->getAction());
        ArrayAssert::that($rule->getParameters())->hasSize(1)->containsKeyAndValue(['file' => 'file_name.js']);
    }

    #[Test]
    public function shouldFindRouteWithNamespace()
    {
        //given
        Route::post('/api/users/:id/archive', 'Controller\\Api\\UsersController', 'archive');
        $router = $this->_createRouter('POST', '/api/users/12/archive');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('Controller\\Api\\UsersController', $rule->getController());
        $this->assertEquals('archive', $rule->getAction());
        ArrayAssert::that($rule->getParameters())->hasSize(1)->containsKeyAndValue(['id' => '12']);
    }

    #[Test]
    public function shouldFindRouteRulePut()
    {
        //given
        Route::put('/user/index', 'Controller\\UserController', 'index');
        $router = $this->_createRouter('PUT', '/user/index');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/index', $rule->getUri());
        $this->assertEquals('PUT', $rule->getMethod());
        $this->assertEquals('Controller\\UserController', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
    }

    #[Test]
    public function shouldFindRouteRuleDelete()
    {
        //given
        Route::delete('/user/:id/delete', 'Controller\\UserController', 'delete');
        $router = $this->_createRouter('DELETE', '/user/12/delete');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/:id/delete', $rule->getUri());
        $this->assertEquals('DELETE', $rule->getMethod());
        $this->assertEquals('Controller\\UserController', $rule->getController());
        $this->assertEquals('delete', $rule->getAction());
        Assert::thatArray($rule->getParameters())->hasSize(1)->containsKeyAndValue(['id' => 12]);
    }

    #[Test]
    public function shouldFindRouteRuleUtf8()
    {
        //given
        Route::post('/api/agents/:login/free', 'Controller\\Api\\AgentsController', 'free');
        $router = $this->_createRouter('POST', '/api/agents/userπ/free');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/api/agents/:login/free', $rule->getUri());
        $this->assertEquals('POST', $rule->getMethod());
        $this->assertEquals('Controller\\Api\\AgentsController', $rule->getController());
        $this->assertEquals('free', $rule->getAction());
    }

    #[Test]
    public function shouldParseParameterWithAtChar()
    {
        //given
        Route::get('/api/agents/:email', 'Controller\\Api\\AgentsController', 'show');
        $router = $this->_createRouter('GET', '/api/agents/john.doe@foo.bar');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('john.doe@foo.bar', $parameters['email']);
    }

    #[Test]
    public function shouldParseParameterWithPercentChar()
    {
        //given
        Route::get("/cabinets/:color/:order_id", "Controller\\SummaryOrderedCorpuses", 'index');
        $router = $this->_createRouter('GET', '/cabinets/Bia%C5%82y%20101/18');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('Bia%C5%82y%20101', $parameters['color']);
        $this->assertEquals('18', $parameters['order_id']);
    }

    #[Test]
    public function shouldParseParameterWithPlusChar()
    {
        //given
        Route::get("/cabinets/:color/:order_id", "Controller\\SummaryOrderedCorpuses", 'index');
        $router = $this->_createRouter('GET', '/cabinets/white+black/18');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('white+black', $parameters['color']);
        $this->assertEquals('18', $parameters['order_id']);
    }

    #[Test]
    public function shouldParseParameterWithSpace()
    {
        //given
        Route::get("/cabinets/:color/:order_id", "Controller\\SummaryOrderedCorpuses", 'index');
        $router = $this->_createRouter('GET', '/cabinets/Biały 101/18');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('Biały 101', $parameters['color']);
        $this->assertEquals('18', $parameters['order_id']);
    }

    public static function requestMethods(): array
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE']
        ];
    }

    public static function requestRestMethods(): array
    {
        return [
            ['GET', '/albums'],
            ['GET', '/albums/new'],
            ['POST', '/albums'],
            ['GET', '/albums/12'],
            ['GET', '/albums/12/edit'],
            ['PUT', '/albums/12'],
            ['PATCH', '/albums/12'],
            ['DELETE', '/albums/12']
        ];
    }

    private function _createRouter($method, $uri)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $pathMock = Mock::create(PathProvider::class);
        Mock::when($pathMock)->getPath()->thenReturn($uri);
        return new Router(new Uri($pathMock));
    }
}
