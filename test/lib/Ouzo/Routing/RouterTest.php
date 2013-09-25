<?php
use Ouzo\Routing\Route;
use Ouzo\Routing\Router;
use Ouzo\Tests\CatchException;

class RouterTest extends PHPUnit_Framework_TestCase
{
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
    public function shouldNotFindIfRequestMethodIsInvalid()
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
    public function shouldNotFindIfRequestIsDifferentFromDeclaration()
    {
        //given
        Route::get('/user/index', 'User#index');
        $router = $this->_createRouter('GET', '/user/indexxx');

        //when
        CatchException::when($router)->findRoute();

        //then
        CatchException::assertThat()->isInstanceOf('Ouzo\Routing\RouterException');
    }

    private function _createRouter($method, $uri)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        return new Router($uri);
    }
}