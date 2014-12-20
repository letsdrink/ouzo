<?php
namespace Ouzo\Api;

use Ouzo\Controller;

class MultipleNsController extends Controller
{
    public function test_action()
    {
    }
}

namespace Ouzo;

use Ouzo\Routing\RouteRule;
use Ouzo\Tests\CatchException;
use PHPUnit_Framework_TestCase;

class SimpleTestController extends Controller
{
}

class ControllerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Config::overrideProperty('namespace', 'controller')->with('\\Ouzo\\');
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
        $routeRule = new RouteRule('GET', '/simple_test/action1', 'simple_test#action1', false);
        $factory = new ControllerFactory();

        $config = Config::getValue('global');
        $_SERVER['REQUEST_URI'] = "{$config['prefix_system']}/simple_test/action1";

        //when
        $currentController = $factory->createController($routeRule);

        //then
        $this->assertEquals('action1', $currentController->currentAction);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenControllerNotFound()
    {
        //given
        $routeRule = new RouteRule('GET', '/simple_test/action', 'not_exists#action', false);
        $factory = new ControllerFactory();

        //when
        CatchException::when($factory)->createController($routeRule);

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\ControllerNotFoundException');
    }

    /**
     * @test
     */
    public function shouldResolveControllerWithNamespace()
    {
        //given
        $routeRule = new RouteRule('GET', '/api/multiple_ns/test_action', 'api/multiple_ns#test_action', true);
        $factory = new ControllerFactory();

        //when
        $currentController = $factory->createController($routeRule);

        //then
        $this->assertInstanceOf('\Ouzo\Api\MultipleNsController', $currentController);
    }
}
