<?php
namespace Ouzo;

use Ouzo\Routing\RouteRule;
use Ouzo\Tests\CatchException;

class SimpleTestController extends Controller
{

}

class ControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldResolveAction()
    {
        //given
        $routeRule = new RouteRule('GET', '/simple_test/action1', 'simple_test#action1', false);
        $factory = new ControllerFactory('\\Ouzo\\');

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
        $factory = new ControllerFactory('\\Ouzo\\');

        //when
        CatchException::when($factory)->createController($routeRule);

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\ControllerNotFoundException');
    }
}