<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Controller;
use Ouzo\ControllerFactory;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Request\RequestParameters;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Arrays;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public static $beforeActionResult;
    public static $actionCalled;
    public static $beforeCallback;

    public function init()
    {
        $this->before[] = 'beforeAction';
        if (self::$beforeCallback) {
            $this->before[] = self::$beforeCallback;
        }
    }

    public function beforeAction()
    {
        return self::$beforeActionResult;
    }

    public function action()
    {
        self::$actionCalled = true;
    }
}

class MockControllerFactory extends ControllerFactory
{
    public function createController(RouteRule $routeRule, RequestParameters $requestParameters)
    {
        $routeRule = Arrays::first(Route::getRoutes());
        return SampleController::createInstance($routeRule, Mock::create(RequestParameters::class));
    }
}

class BeforeFilterTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        Route::clear();
        SampleController::$beforeCallback = null;
    }

    protected function frontControllerBindings(InjectorConfig $config)
    {
        parent::frontControllerBindings($config);
        $config->bind(ControllerFactory::class)->toInstance(new MockControllerFactory());
    }

    /**
     * @test
     */
    public function shouldNotInvokeActionWhenBeforeFilterReturnsFalse()
    {
        //given
        SampleController::$beforeActionResult = false;
        SampleController::$actionCalled = false;
        Route::any('/sample/action', 'sample#action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertFalse(SampleController::$actionCalled);
    }

    /**
     * @test
     */
    public function shouldInvokeActionWhenBeforeFilterReturnsTrue()
    {
        //given
        SampleController::$beforeActionResult = true;
        SampleController::$actionCalled = false;
        Route::any('/sample/action', 'sample#action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertTrue(SampleController::$actionCalled);
    }

    /**
     * @test
     */
    public function shouldInvokeFunctionCallback()
    {
        //given
        SampleController::$beforeActionResult = true;
        SampleController::$beforeCallback = function ($controller) {
            $controller->redirect('url');
            return true;
        };

        Route::any('/sample/action', 'sample#action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertRedirectsTo('url');
    }
}
