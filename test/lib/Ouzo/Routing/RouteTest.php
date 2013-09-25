<?php
use Ouzo\Routing\Route;
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
        $return = Route::getRoutes();

        //then
        $this->assertCount(2, $return);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectAboutRouteRule()
    {
        //given
        Route::get('/user/index', 'User#index');

        //when
        $return = Arrays::first(Route::getRoutes());

        //then
        $this->assertEquals('/user/index', $return->getUri());
        $this->assertEquals('User', $return->getController());
        $this->assertEquals('index', $return->getAction());
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
        $return = Route::getRoutes();

        //then
        $this->assertCount(2, $return);
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
        $return = Route::getRoutes();

        //then
        $this->assertCount(2, $return);
    }

    /**
     * @test
     */
    public function shouldSearchRouteForController()
    {
        //given
        Route::any('/user/save', 'User#save');
        Route::any('/user/update/id/:id', 'User#update');
        Route::any('/photo', 'Photo');

        //when
        $controllerRoutes = Route::getRoutesForController('user');

        //then
        $this->assertCount(2, $controllerRoutes);
        $this->assertCount(3, Route::getRoutes());
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayIfNotFoundRoutesForController()
    {
        //given
        Route::any('/user/save', 'User#save');
        Route::any('/user/update/id/:id', 'User#update');

        //when
        $controllerRoutes = Route::getRoutesForController('photo');

        //then
        $this->assertEmpty($controllerRoutes);
    }
}