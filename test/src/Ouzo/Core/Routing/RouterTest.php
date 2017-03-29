<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Routing\Route;
use Ouzo\Routing\Router;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Uri;

class RouterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Route::clear();
    }

    /**
     * @test
     */
    public function shouldFindRouteGet()
    {
        //given
        Route::get('/user/index', 'User#index');
        $router = $this->_createRouter('GET', '/user/index');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/index', $rule->getUri());
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('User', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
    }

    /**
     * @test
     */
    public function shouldNotMatchOnlyPrefixForGet()
    {
        //given
        Route::get('/user/:id', 'User#show');
        Route::get('/user/:id/posts', 'User#posts');
        $router = $this->_createRouter('GET', '/user/1/posts');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('posts', $rule->getAction());
    }

    /**
     * @test
     */
    public function shouldNotMatchControllerPrefixForAllowAll()
    {
        //given
        Route::allowAll('/user', 'User');
        $router = $this->_createRouter('GET', '/userInvalid/index');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf('Ouzo\Routing\RouterException');
    }

    /**
     * @test
     */
    public function shouldNotFindRouteIfRequestMethodIsInvalid()
    {
        //given
        Route::get('/user/index', 'User#index');
        $router = $this->_createRouter('POST', '/user/index');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf('Ouzo\Routing\RouterException');
    }

    /**
     * @test
     */
    public function shouldNotFindRouteIfRequestIsDifferentFromDeclaration()
    {
        //given
        Route::get('/user/index', 'User#index');
        $router = $this->_createRouter('GET', '/user/indexxx');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf('Ouzo\Routing\RouterException');
    }

    /**
     * @test
     */
    public function shouldFindRouteWithPlaceholderValue()
    {
        //given
        Route::get('/user/show/id/:id', 'User#show');
        $router = $this->_createRouter('GET', '/user/show/id/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/show/id/:id', $rule->getUri());
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('User', $rule->getController());
        $this->assertEquals('show', $rule->getAction());
    }

    /**
     * @test
     */
    public function shouldNotFindRouteWhenRequestAndDefinitionIsNotEqualWithPlaceholder()
    {
        //given
        Route::get('/user/show/id/:id', 'User#show');
        $router = $this->_createRouter('GET', '/user/show/id/12/surname/smith');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf('Ouzo\Routing\RouterException');
    }

    /**
     * @test
     */
    public function shouldFindRoutePost()
    {
        //given
        Route::post('/user/save', 'User#save');
        $router = $this->_createRouter('POST', '/user/save');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/save', $rule->getUri());
        $this->assertEquals('POST', $rule->getMethod());
        $this->assertEquals('User', $rule->getController());
        $this->assertEquals('save', $rule->getAction());
    }

    /**
     * @test
     * @dataProvider requestMethods
     * @param string $method
     */
    public function shouldFindRouteAny($method)
    {
        //given
        Route::any('/user/save', 'User#save');
        $router = $this->_createRouter($method, '/user/save');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/save', $rule->getUri());
        $this->assertContains($method, $rule->getMethod());
        $this->assertEquals('User', $rule->getController());
        $this->assertEquals('save', $rule->getAction());
    }

    /**
     * @test
     * @dataProvider requestRestMethods
     * @param string $method
     * @param string $uri
     */
    public function shouldFindRouteResource($method, $uri)
    {
        //given
        Route::resource('albums');
        $router = $this->_createRouter($method, $uri);

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals($method, $rule->getMethod());
        $this->assertEquals('albums', $rule->getController());
    }

    /**
     * @test
     */
    public function shouldFindRouteForAllInController()
    {
        //given
        Route::allowAll('/users', 'users');
        $router = $this->_createRouter('GET', '/users/select_for_user');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertFalse($rule->isActionRequired());
        $this->assertEquals('users', $rule->getController());
    }

    /**
     * @test
     */
    public function shouldNotFindRouteWhenExceptAction()
    {
        //given
        Route::allowAll('/users', 'users', ['except' => ['add']]);
        $router = $this->_createRouter('GET', '/users/add');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat();
    }

    /**
     * @test
     */
    public function shouldFindRouteWhenDeclaredAnIsInExcept()
    {
        //given
        Route::get('/users/add', 'users#add');
        Route::allowAll('/users', 'users', ['except' => ['add']]);
        $router = $this->_createRouter('GET', '/users/add');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertNotEmpty($rule);
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('add', $rule->getAction());
        $this->assertEquals('users', $rule->getController());
    }

    /**
     * @test
     */
    public function shouldFindRouteAndGetParamsFromPath()
    {
        //given
        Route::get('/users/show/id/:id/call_id/:call_id', 'users#show');
        $router = $this->_createRouter('GET', '/users/show/id/1/call_id/2');

        //when
        $rule = $router->findRoute();

        //then
        Assert::thatArray($rule->getParameters())->hasSize(2)->containsKeyAndValue(['id' => 1, 'call_id' => 2]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRestIndex()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('GET', '/users');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
        $this->assertEmpty($rule->getParameters());
    }

    /**
     * @test
     */
    public function shouldFindRouteRestNew()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('GET', '/users/fresh');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('fresh', $rule->getAction());
        $this->assertEmpty($rule->getParameters());
    }

    /**
     * @test
     */
    public function shouldFindRouteRestCreate()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('POST', '/users');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('create', $rule->getAction());
        $this->assertEmpty($rule->getParameters());
    }

    /**
     * @test
     */
    public function shouldFindRouteRestShow()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('GET', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('show', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRestEdit()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('GET', '/users/12/edit');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('edit', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRestUpdatePut()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('PUT', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('update', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRestUpdatePatch()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('PATCH', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('update', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRestDestroy()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('DELETE', '/users/12');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('destroy', $rule->getAction());
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(['id' => 12]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRuleWithDefaultRoute()
    {
        //given
        Route::resource('users');
        $router = $this->_createRouter('GET', '/users/');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('users', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
    }

    /**
     * @test
     */
    public function shouldFindRouteWithSpecialCharactersInParameter()
    {
        //given
        Route::get('/resources/:file', 'resources#server');
        $router = $this->_createRouter('GET', '/resources/file_name.js');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('resources', $rule->getController());
        $this->assertEquals('server', $rule->getAction());
        ArrayAssert::that($rule->getParameters())->hasSize(1)->containsKeyAndValue(['file' => 'file_name.js']);
    }

    /**
     * @test
     */
    public function shouldFindRouteWithNamespace()
    {
        //given
        Route::post('/api/users/:id/archive', 'api/users#archive');
        $router = $this->_createRouter('POST', '/api/users/12/archive');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('api/users', $rule->getController());
        $this->assertEquals('archive', $rule->getAction());
        ArrayAssert::that($rule->getParameters())->hasSize(1)->containsKeyAndValue(['id' => '12']);
    }

    /**
     * @test
     */
    public function shouldFindRouteRulePut()
    {
        //given
        Route::put('/user/index', 'user#index');
        $router = $this->_createRouter('PUT', '/user/index');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/index', $rule->getUri());
        $this->assertEquals('PUT', $rule->getMethod());
        $this->assertEquals('user', $rule->getController());
        $this->assertEquals('index', $rule->getAction());
    }

    /**
     * @test
     */
    public function shouldFindRouteRuleDelete()
    {
        //given
        Route::delete('/user/:id/delete', 'user#delete');
        $router = $this->_createRouter('DELETE', '/user/12/delete');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user/:id/delete', $rule->getUri());
        $this->assertEquals('DELETE', $rule->getMethod());
        $this->assertEquals('user', $rule->getController());
        $this->assertEquals('delete', $rule->getAction());
        Assert::thatArray($rule->getParameters())->hasSize(1)->containsKeyAndValue(['id' => 12]);
    }

    /**
     * @test
     */
    public function shouldFindRouteRuleUtf8()
    {
        //given
        Route::post('/api/agents/:login/free', 'api/agents#free');
        $router = $this->_createRouter('POST', '/api/agents/userπ/free');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/api/agents/:login/free', $rule->getUri());
        $this->assertEquals('POST', $rule->getMethod());
        $this->assertEquals('api/agents', $rule->getController());
        $this->assertEquals('free', $rule->getAction());
    }

    /**
     * @test
     */
    public function shouldParseParameterWithAtChar()
    {
        //given
        Route::get('/api/agents/:email', 'api/agents#show');
        $router = $this->_createRouter('GET', '/api/agents/john.doe@foo.bar');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('john.doe@foo.bar', $parameters['email']);
    }

    /**
     * @test
     */
    public function shouldParseParameterWithPercentChar()
    {
        //given
        Route::get("/cabinets/:color/:order_id", "SummaryOrderedCorpuses#index");
        $router = $this->_createRouter('GET', '/cabinets/Bia%C5%82y%20101/18');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('Bia%C5%82y%20101', $parameters['color']);
        $this->assertEquals('18', $parameters['order_id']);
    }

    /**
     * @test
     */
    public function shouldParseParameterWithSpace()
    {
        //given
        Route::get("/cabinets/:color/:order_id", "SummaryOrderedCorpuses#index");
        $router = $this->_createRouter('GET', '/cabinets/Biały 101/18');

        //when
        $rule = $router->findRoute();

        //then
        $parameters = $rule->getParameters();
        $this->assertEquals('Biały 101', $parameters['color']);
        $this->assertEquals('18', $parameters['order_id']);
    }

    public function requestMethods()
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE']
        ];
    }

    public function requestRestMethods()
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
        $pathMock = Mock::create('\Ouzo\Uri\PathProvider');
        Mock::when($pathMock)->getPath()->thenReturn($uri);
        return new Router(new Uri($pathMock));
    }
}
