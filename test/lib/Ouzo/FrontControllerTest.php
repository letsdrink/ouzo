<?php
namespace Ouzo;

use Exception;
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\Routing\Route;
use Ouzo\Tests\ControllerTestCase;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public function action()
    {
        echo "OUTPUT";
    }

    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }

    public function save()
    {
        $this->layout->renderAjax('save');
        $this->layout->unsetLayout();
    }

    public function except()
    {
        $this->layout->renderAjax('except');
        $this->layout->unsetLayout();
    }
}

class FrontControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerResolver = new ControllerResolver('\\Ouzo\\');
        $this->_frontController->redirectHandler = $this->getMock('\Ouzo\RedirectHandler', array('redirect'));
        Route::$routes = array();
    }

    /**
     * @test
     */
    public function shouldNotDisplayOutput()
    {
        //given
        Route::allowAll('/sample', 'sample');

        //when
        $this->get('/sample/action');

        //then
        $this->expectOutputString('');
    }

    /**
     * @test
     */
    public function shouldCheckRouteGetIfRequestValid()
    {
        //given
        Route::get('/sample/save', 'sample#save');

        //when
        $this->get('/sample/save');

        //then
        $this->assertRendersContent('save');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionIfNoRouteFound()
    {
        //given
        Route::post('/sample/save', 'sample#save');

        //when
        try {
            $this->get('/sample/save');
            $this->fail();
        } catch (Routing\RouterException $e) {
        }
    }

    /**
     * @test
     */
    public function shouldExceptActionInAllAllow()
    {
        //given
        Route::allowAll('/sample', 'sample', array('except'));

        //when
        try {
            $this->get('/sample/except');
            $this->fail();
        } catch (Routing\RouterException $e) {
        }

        //then
    }
}