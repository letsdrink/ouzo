<?php
namespace Ouzo;

use Exception;
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\Routing\Route;
use Ouzo\Tests\CatchException;
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

class RestfulController extends Controller
{
    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }

    public function fresh()
    {
        $this->layout->renderAjax('fresh');
        $this->layout->unsetLayout();
    }

    public function create()
    {
        $this->layout->renderAjax('create');
        $this->layout->unsetLayout();
    }

    public function show()
    {
        $this->layout->renderAjax('show=' . $this->params['id']);
        $this->layout->unsetLayout();
    }

    public function edit()
    {
        $this->layout->renderAjax('edit=' . $this->params['id']);
        $this->layout->unsetLayout();
    }

    public function update()
    {
        $this->layout->renderAjax('update=' . $this->params['id']);
        $this->layout->unsetLayout();
    }

    public function destroy()
    {
        $this->layout->renderAjax('destroy=' . $this->params['id']);
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

    /**
     * @test
     */
    public function shouldRouteWithQueryString()
    {
        //given
        Route::get('/sample/save', 'sample#save');

        //when
        $this->get('/sample/save?hash=1235');

        //then
        $this->assertRendersContent('save');
    }

    /**
     * @test
     */
    public function shouldRouteRestIndexWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful');

        //then
        $this->assertRendersContent('index');
    }

    /**
     * @test
     */
    public function shouldRouteRestIndexWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->post('/restful', array());

        //then
        $this->assertRendersNotEqualContent('index');
    }

    /**
     * @test
     */
    public function shouldRouteRestFreshWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful/fresh');

        //then
        $this->assertRendersContent('fresh');
    }

    /**
     * @test
     */
    public function shouldRouteRestFreshWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        CatchException::when($this)->post('/restful/fresh', array());

        //then
        CatchException::assertThat();
    }

    /**
     * @test
     */
    public function shouldRouteRestCreateWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->post('/restful', array());

        //then
        $this->assertRendersContent('create');
    }

    /**
     * @test
     */
    public function shouldRouteRestCreateWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful');

        //then
        $this->assertRendersNotEqualContent('create');
    }

    /**
     * @test
     */
    public function shouldRouteRestShowWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful/12', array());

        //then
        $this->assertRendersContent('show=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestShowWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        CatchException::when($this)->post('/restful/12', array());

        //then
        CatchException::assertThat();
    }

    /**
     * @test
     */
    public function shouldRouteRestEditWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful/12/edit', array());

        //then
        $this->assertRendersContent('edit=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestEditWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        CatchException::when($this)->post('/restful/12/edit');

        //then
        CatchException::assertThat();
    }

    /**
     * @test
     */
    public function shouldRouteRestPutWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->put('/restful/12', array());

        //then
        $this->assertRendersContent('update=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestPutWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful/12');

        //then
        $this->assertRendersNotEqualContent('update=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestPatchWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->patch('/restful/12', array());

        //then
        $this->assertRendersContent('update=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestPatchWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->get('/restful/12');

        //then
        $this->assertRendersNotEqualContent('update=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestDeleteWithCorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->delete('/restful/12', array());

        //then
        $this->assertRendersContent('destroy=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestDeleteWithIncorrectMethod()
    {
        //given
        Route::resource('restful');

        //when
        $this->patch('/restful/12', array());

        //then
        $this->assertRendersNotEqualContent('destroy=12');
    }
}