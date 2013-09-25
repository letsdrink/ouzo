<?php
use Ouzo\Routing\Route;
use Ouzo\Utilities\Arrays;

class RouteTest extends PHPUnit_Framework_TestCase
{
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
        Route::$routes = array();
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
        Route::$routes = array();
        Route::any('/user/save', 'User#save');
        Route::any('/user/update/id/:id', 'User#update');

        //when
        $return = Route::getRoutes();

        //then
        $this->assertCount(2, $return);
    }
}