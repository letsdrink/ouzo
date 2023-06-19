<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Api\SomeController;
use Ouzo\Config;
use Ouzo\FrontController;
use Ouzo\Middleware\Interceptor\DefaultRequestId;
use Ouzo\Middleware\MiddlewareRepository;
use Ouzo\RestfulController;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouterException;
use Ouzo\SampleFrontController;
use Ouzo\Session;
use Ouzo\Tests\ArrayAssert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Utilities\Arrays;

class FrontControllerTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Route::clear();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Config::clearProperty('debug');
    }

    /**
     * @test
     */
    public function shouldCheckRouteGetIfRequestValid()
    {
        //given
        Route::get('/sample/save', SampleFrontController::class, 'save');

        //when
        $this->get('/sample/save');

        //then
        $this->assertRenderedContent()->isEqualTo('save');
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldThrowExceptionIfNoRouteFound()
    {
        //given
        Route::post('/sample/save', SampleFrontController::class, 'save');

        //when
        CatchException::when($this)->get('/sample/save');
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    /**
     * @test
     */
    public function shouldExceptActionInAllAllow()
    {
        //given
        Route::allowAll('/sample', SampleFrontController::class, ['except' => ['except']]);

        //when
        try {
            $this->get('/sample/except');
            $this->fail();
        } catch (RouterException $e) {//then
            $this->assertEquals('No route rule found for HTTP method [GET] and URI [/sample/except]', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function shouldRouteWithQueryString()
    {
        //given
        Route::get('/sample/save', SampleFrontController::class, 'save');

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
        Route::resource(RestfulController::class, 'restful');

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
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->post('/restful', []);

        //then
        $this->assertRenderedContent()->isNotEqualTo('index');
    }

    /**
     * @test
     */
    public function shouldRouteRestFreshWithCorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->get('/restful/fresh');

        //then
        $this->assertRenderedContent()->isEqualTo('fresh');
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldRouteRestFreshWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        CatchException::when($this)->post('/restful/fresh', []);

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    /**
     * @test
     */
    public function shouldRouteRestCreateWithCorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->post('/restful', []);

        //then
        $this->assertRenderedContent()->isEqualTo('create');
    }

    /**
     * @test
     */
    public function shouldRouteRestCreateWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

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
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->get('/restful/12', []);

        //then
        $this->assertRenderedContent()->isEqualTo('show=12');
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldRouteRestShowWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        CatchException::when($this)->post('/restful/12', []);

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    /**
     * @test
     */
    public function shouldRouteRestEditWithCorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->get('/restful/12/edit', []);

        //then
        $this->assertRenderedContent()->isEqualTo('edit=12');
    }

    /**
     * @test
     * @throws Exception
     */
    public function shouldRouteRestEditWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        CatchException::when($this)->post('/restful/12/edit', []);

        //then
        CatchException::assertThat()->isInstanceOf(RouterException::class);
    }

    /**
     * @test
     */
    public function shouldRouteRestPutWithCorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->put('/restful/12', []);

        //then
        $this->assertRenderedContent()->isEqualTo('update=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestPutWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

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
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->patch('/restful/12');

        //then
        $this->assertRenderedContent()->isEqualTo('update=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestPatchWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

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
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->delete('/restful/12');

        //then
        $this->assertRenderedContent()->isEqualTo('destroy=12');
    }

    /**
     * @test
     */
    public function shouldRouteRestDeleteWithIncorrectMethod()
    {
        //given
        Route::resource(RestfulController::class, 'restful');

        //when
        $this->patch('/restful/12');

        //then
        $this->assertRenderedContent()->isNotEqualTo('destroy=12');
    }

    /**
     * @test
     */
    public function shouldRemoveDuplicatedPrefixFromUrlWhenExists()
    {
        //given
        Route::post('/sample/redirect_to', SampleFrontController::class, 'redirect_to');

        //when
        $this->post('/sample/redirect_to', []);

        //then
        $this->assertRedirectsTo('/sample/add');
    }

    /**
     * @test
     */
    public function shouldRouteToRoot()
    {
        //given
        Route::get('/', SampleFrontController::class, 'index');

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
        Route::resource(RestfulController::class, 'restful');
        $this->get('/restful');

        //when
        $currentController = $this->requestContext()->getCurrentController();

        //then
        $this->assertEquals(RestfulController::class, $currentController);
    }

    /**
     * @test
     */
    public function shouldTraceRequestInfo()
    {
        //given
        Config::overrideProperty('debug')->with(true);
        Route::resource(RestfulController::class, 'restful');
        $this->get('/restful?param=1');

        //when
        $queries = Arrays::first(Session::get('stats_queries'));

        //then
        ArrayAssert::that($queries['request_params'][0])->hasSize(1)->containsKeyAndValue(['param' => 1]);
    }

    /**
     * @test
     */
    public function shouldHandleControllerInNamespace()
    {
        //given
        Route::get('/api/some/action', SomeController::class, 'action');

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
        $middlewareRepository = new MiddlewareRepository();
        $middlewareRepository->add(new ExceptionThrowMiddleware());
        $this->injectorConfig->bind(MiddlewareRepository::class)->toInstance($middlewareRepository);
        Route::get('/sample/save', SampleFrontController::class, 'save');

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
        Route::get('/sample/save', SampleFrontController::class, 'save');

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
        Route::get('/sample/save', SampleFrontController::class, 'save');

        //when
        $this->get('/sample/save');

        //then
        $this->assertNotEmpty(Session::get('stats_queries'));
    }

    /**
     * @test
     */
    public function shouldAddSomeExampleFieldIntoRequestObject()
    {
        //given
        Route::get('/sample/save', SampleFrontController::class, 'save');
        $interceptor = new SampleMiddleware();
        $middlewareRepository = new MiddlewareRepository();
        $middlewareRepository->add($interceptor);

        $this->injectorConfig->bind(MiddlewareRepository::class)->toInstance($middlewareRepository);

        //when
        $this->get('/sample/save');

        //then
        $this->assertEquals('SampleMiddleware', $interceptor->getData());
        $this->assertRenderedContent()->isEqualTo('save');
    }

    /**
     * @test
     */
    public function shouldSetRequestIdUsingMiddleware()
    {
        //given
        Route::get('/sample/save', SampleFrontController::class, 'save');
        $middlewareRepository = new MiddlewareRepository();
        $middlewareRepository->add(new DefaultRequestId());

        $this->injectorConfig->bind(MiddlewareRepository::class)->toInstance($middlewareRepository);

        //when
        $this->get('/sample/save');

        //then
        $this->assertNotNull(FrontController::$requestId);
    }

    public function _afterInitCallback()
    {
        throw new Exception("afterInitCallback");
    }
}
