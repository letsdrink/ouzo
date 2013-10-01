<?php

use Ouzo\Routing\Route;
use Ouzo\Routing\Router;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Uri;

class RouterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        Route::$routes = array();
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
        Route::allowAll('/users', 'users', array('except' => array('add')));
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
        Route::allowAll('/users', 'users', array('except' => array('add')));
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
        Assert::thatArray($rule->getParameters())->hasSize(2)->containsKeyAndValue(array('id' => 1, 'call_id' => 2));
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
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(array('id' => 12));
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
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(array('id' => 12));
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
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(array('id' => 12));
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
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(array('id' => 12));
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
        Assert::thatArray($rule->getParameters())->containsKeyAndValue(array('id' => 12));
    }

    public function requestMethods()
    {
        return array(
            array('GET'),
            array('POST'),
            array('PUT'),
            array('PATCH'),
            array('DELETE')
        );
    }

    public function requestRestMethods()
    {
        return array(
            array('GET', '/albums'),
            array('GET', '/albums/new'),
            array('POST', '/albums'),
            array('GET', '/albums/12'),
            array('GET', '/albums/12/edit'),
            array('PUT', '/albums/12'),
            array('PATCH', '/albums/12'),
            array('DELETE', '/albums/12')
        );
    }

    private function _createRouter($method, $uri)
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $pathMock = $this->getMock('\Ouzo\Uri\PathProvider', array('getPath'));
        $pathMock->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($uri));

        return new Router(new Uri($pathMock));
    }
}