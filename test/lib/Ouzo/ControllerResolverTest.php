<?php

namespace Ouzo;


class SimpleTestController extends Controller
{

}

class ControllerResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldResolveAction()
    {
        //given
        $controllerResolver = new ControllerResolver('\\Ouzo\\');

        $config = Config::getValue('global');
        $_SERVER['REQUEST_URI'] = "{$config['prefix_system']}/simple_test/action1";

        //when
        $currentController = $controllerResolver->getController('simple_test', 'action1');

        //then
        $this->assertEquals('action1', $currentController->currentAction);
    }
}
