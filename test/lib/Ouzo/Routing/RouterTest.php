<?php

use Ouzo\Routing\Route;
use Ouzo\Routing\Router;
use Ouzo\Tests\CatchException;

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
    public function shouldFindRouteOnlyForController()
    {
        //given
        Route::get('/user', 'User');
        $router = $this->_createRouter('GET', '/user/show/id/12/surname/smith');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user', $rule->getUri());
        $this->assertEquals('GET', $rule->getMethod());
        $this->assertEquals('User', $rule->getController());
        $this->assertNull($rule->getAction());
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
     * @dataProvider requestMethods
     */
    public function shouldFindRouteAnyForController($method)
    {
        //given
        Route::any('/user', 'User');
        $router = $this->_createRouter($method, '/user/save');

        //when
        $rule = $router->findRoute();

        //then
        $this->assertEquals('/user', $rule->getUri());
        $this->assertContains($method, $rule->getMethod());
        $this->assertEquals('User', $rule->getController());
        $this->assertNull($rule->getAction());
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

    private function _createRouter($method, $uri)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        return new Router($uri);
    }
}