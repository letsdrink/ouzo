<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Api;

use Ouzo\Controller;

class MultipleNsController extends Controller
{
    public function test_action()
    {
    }
}

namespace Ouzo;

use Ouzo\Api\MultipleNsController;
use Ouzo\Injection\Injector;
use Ouzo\Request\RequestParameters;
use Ouzo\Request\RoutingService;
use Ouzo\Routing\RouteRule;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;

class SimpleTestController extends Controller
{
}

class ControllerFactoryTest extends TestCase
{
    private $injector;

    public function setUp()
    {
        parent::setUp();
        Config::overrideProperty('namespace', 'controller')->with('\\Ouzo\\');
        $this->injector = new Injector();
    }

    public function tearDown()
    {
        parent::tearDown();
        Config::clearProperty('namespace', 'controller');
    }

    /**
     * @test
     */
    public function shouldResolveAction()
    {
        //given
        $routingService = Mock::create(RoutingService::class);

        $config = Config::getValue('global');
        $_SERVER['REQUEST_URI'] = "{$config['prefix_system']}/simple_test/action1";
        Mock::when($routingService)->getUri()->thenReturn(new Uri());

        $routeRule = new RouteRule('GET', '/simple_test/action1', 'simple_test', 'action1', false);
        Mock::when($routingService)->getRouteRule()->thenReturn($routeRule);

        $factory = $this->injector->getInstance(ControllerFactory::class);

        //when
        $currentController = $factory->createController($routeRule, new RequestParameters($routingService));

        //then
        $this->assertEquals('action1', $currentController->currentAction);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenControllerNotFound()
    {
        //given
        $routingService = Mock::create(RoutingService::class);
        Mock::when($routingService)->getUri()->thenReturn(new Uri());

        $routeRule = new RouteRule('GET', '/simple_test/action', 'not_exists', 'action', false);
        Mock::when($routingService)->getRouteRule()->thenReturn($routeRule);

        $factory = $this->injector->getInstance(ControllerFactory::class);

        //when
        CatchException::when($factory)->createController($routeRule, new RequestParameters($routingService));

        //then
        CatchException::assertThat()->isInstanceOf(ControllerNotFoundException::class);
        CatchException::assertThat()
            ->hasMessage('Controller [NotExists] for URI [/simple_test/action] does not exist!');
    }

    /**
     * @test
     */
    public function shouldResolveControllerWithNamespace()
    {
        //given
        $routingService = Mock::create(RoutingService::class);
        Mock::when($routingService)->getUri()->thenReturn(new Uri());

        $routeRule = new RouteRule('GET', '/api/multiple_ns/test_action', 'api/multiple_ns', 'test_action', true);
        Mock::when($routingService)->getRouteRule()->thenReturn($routeRule);

        $factory = $this->injector->getInstance(ControllerFactory::class);

        //when
        $currentController = $factory->createController($routeRule, new RequestParameters($routingService));

        //then
        $this->assertInstanceOf(MultipleNsController::class, $currentController);
    }
}
