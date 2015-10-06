<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Api;

use Ouzo\Controller;

class SomeController extends Controller
{
    public function action()
    {
        $this->layout->renderAjax('some controller - action');
        $this->layout->unsetLayout();
    }
}

namespace Ouzo;

use Exception;
use Ouzo\Db\Stats;
use Ouzo\Request\RequestContext;
use Ouzo\Routing\Route;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Arrays;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public function action()
    {
        echo "OUTPUT";
        $this->header('Location : http://foo.com');
    }

    public function redirect_to()
    {
        $this->redirect('/sample/add');
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
    public function __construct()
    {
        Config::overrideProperty('namespace', 'controller')->with('\\Ouzo\\');
        parent::__construct();
    }

    public function setUp()
    {
        parent::setUp();
        Route::$routes = array();
    }

    public function tearDown()
    {
        parent::tearDown();
        Config::clearProperty('namespace', 'controller');
        Config::clearProperty('debug');
        Config::clearProperty('callback', 'afterControllerInit');
    }

    /**
     * @test
     */
    public function shouldNotDisplayOutputBeforeHeadersAreSent()
    {
        //given
        $self = $this;
        $this->_frontController->headerSender = Mock::mock();

        $obLevel = ob_get_level();
        Mock::when($this->_frontController->headerSender)->send(Mock::any())->thenAnswer(function () use ($self, $obLevel) {
            //if there's a nested buffer, nothing was sent to output
            $self->assertTrue(ob_get_level() > $obLevel);
            $self->assertEquals('OUTPUT', ob_get_contents());
        });

        Route::allowAll('/sample', 'sample');

        //when
        $this->get('/sample/action');

        //then no exceptions
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
        $this->assertRenderedContent()->isEqualTo('save');
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
        Route::allowAll('/sample', 'sample', array('except' => array('except')));

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
        $this->assertRenderedContent()->isEqualTo('save');
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
        $this->assertRenderedContent()->isEqualTo('index');
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
        $this->assertRenderedContent()->isNotEqualTo('index');
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
        $this->assertRenderedContent()->isEqualTo('fresh');
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
        $this->assertRenderedContent()->isEqualTo('create');
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
        $this->assertRenderedContent()->isNotEqualTo('create');
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
        $this->assertRenderedContent()->isEqualTo('show=12');
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
        $this->assertRenderedContent()->isEqualTo('edit=12');
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
        $this->assertRenderedContent()->isEqualTo('update=12');
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
        $this->assertRenderedContent()->isNotEqualTo('update=12');
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
        $this->assertRenderedContent()->isEqualTo('update=12');
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
        $this->assertRenderedContent()->isNotEqualTo('update=12');
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
        $this->assertRenderedContent()->isEqualTo('destroy=12');
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
        $this->assertRenderedContent()->isNotEqualTo('destroy=12');
    }

    /**
     * @test
     */
    public function shouldRemoveDuplicatedPrefixFromUrlWhenExists()
    {
        //given
        Route::post('/sample/redirect_to', 'sample#redirect_to');

        //when
        $this->post('/sample/redirect_to', array());

        //then
        $this->assertRedirectsTo('/sample/add');
    }

    /**
     * @test
     */
    public function shouldRouteToRoot()
    {
        //given
        Route::get('/', 'sample#index');

        //when
        $this->get('/');

        //then
        $this->assertRenderedContent()->isEqualTo('index');
    }

    /**
     * @test
     */
    public function shouldGetCurrentRequestContextController()
    {
        //given
        Route::resource('restful');
        $this->get('/restful');

        //when
        $currentController = RequestContext::getCurrentController();

        //then
        $this->assertEquals('restful', $currentController);
    }

    /**
     * @test
     */
    public function shouldTraceRequestInfo()
    {
        //given
        Config::overrideProperty('debug')->with(true);
        Route::resource('restful');
        $this->get('/restful?param=1');

        //when
        $queries = Arrays::first(Stats::queries());

        //then
        ArrayAssert::that($queries['request_params'][0]->toArray())->hasSize(1)->containsKeyAndValue(array('param' => 1));
    }

    /**
     * @test
     */
    public function shouldHandleControllerInNamespace()
    {
        //given
        Route::get('/api/some/action', 'api/some#action');

        //when
        $this->get('/api/some/action');

        //then
        $this->assertRenderedContent()->isEqualTo('some controller - action');
    }

    /**
     * @test
     */
    public function shouldCallbackInvokeAfterInit()
    {
        //given
        $callback = array($this, '_afterInitCallback');
        Config::overrideProperty('callback', 'afterControllerInit')->with($callback);
        Route::get('/sample/save', 'sample#save');

        //when
        CatchException::when($this)->get('/sample/save');

        //then
        CatchException::assertThat()->hasMessage("afterInitCallback");
    }

    /**
     * @test
     */
    public function shouldNotSaveStatsIfDebugDisabled()
    {
        //given
        Config::overrideProperty('debug')->with(false);
        Session::remove('stats_queries');
        Route::get('/sample/save', 'sample#save');

        //when
        $this->get('/sample/save');

        //then
        $this->assertEmpty(Session::get('stats_queries'));
    }

    /**
     * @test
     */
    public function shouldSaveStatsIfDebugIsOn()
    {
        //given
        Config::overrideProperty('debug')->with(true);
        Session::remove('stats_queries');
        Route::get('/sample/save', 'sample#save');

        //when
        $this->get('/sample/save');

        //then
        $this->assertNotEmpty(Session::get('stats_queries'));
    }

    public function _afterInitCallback()
    {
        throw new Exception("afterInitCallback");
    }
}
