<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Controller;
use Ouzo\ControllerFactory;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Request\RequestParameters;
use Ouzo\Request\RoutingService;
use Ouzo\Routing\Route;
use Ouzo\Routing\RouteRule;
use Ouzo\Stats\SessionStats;
use Ouzo\Tests\ControllerTestCase;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Uri;
use Ouzo\Uri\PathProvider;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\Attributes\Test;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public static ?bool $beforeActionResult = null;
    public static bool $actionCalled;
    public static ?Closure $beforeCallback = null;

    public function init(): void
    {
        $this->before[] = 'beforeAction';
        if (self::$beforeCallback) {
            $this->before[] = self::$beforeCallback;
        }
    }

    public function beforeAction(): ?bool
    {
        return self::$beforeActionResult;
    }

    public function action(): void
    {
        self::$actionCalled = true;
    }
}

class MockControllerFactory extends ControllerFactory
{
    public function createController(RouteRule $routeRule, RequestParameters $requestParameters, SessionStats $sessionStats): Controller
    {
        $routeRule = Arrays::first(Route::getRoutes());

        $routingService = Mock::create(RoutingService::class);
        Mock::when($routingService)->getUri()->thenReturn(new Uri(new PathProvider()));

        $requestParametersMock = Mock::create(RequestParameters::class);
        Mock::when($requestParametersMock)->getRoutingService()->thenReturn($routingService);

        $sessionStats = Mock::create(SessionStats::class);

        return SampleController::createInstance($routeRule, $requestParametersMock, $sessionStats);
    }
}

class BeforeFilterTest extends ControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Route::clear();
        SampleController::$beforeCallback = null;
    }

    protected function frontControllerBindings(InjectorConfig $config): void
    {
        parent::frontControllerBindings($config);
        $config->bind(ControllerFactory::class)->toInstance(new MockControllerFactory());
    }

    #[Test]
    public function shouldNotInvokeActionWhenBeforeFilterReturnsFalse()
    {
        //given
        SampleController::$beforeActionResult = false;
        SampleController::$actionCalled = false;
        Route::any('/sample/action', SampleController::class, 'action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertFalse(SampleController::$actionCalled);
    }

    #[Test]
    public function shouldInvokeActionWhenBeforeFilterReturnsTrue()
    {
        //given
        SampleController::$beforeActionResult = true;
        SampleController::$actionCalled = false;
        Route::any('/sample/action', SampleController::class, 'action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertTrue(SampleController::$actionCalled);
    }

    #[Test]
    public function shouldInvokeFunctionCallback()
    {
        //given
        SampleController::$beforeActionResult = true;
        SampleController::$beforeCallback = function ($controller) {
            $controller->redirect('url');
            return true;
        };

        Route::any('/sample/action', SampleController::class, 'action');

        //when
        $this->get('/sample/action');

        //then
        $this->assertRedirectsTo('url');
    }
}
