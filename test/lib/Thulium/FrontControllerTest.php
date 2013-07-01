<?php

use Thulium\Config;
use Thulium\Controller;
use Thulium\Tests\ControllerTestCase;

class SampleControllerException extends Exception
{

}

class SampleController extends Controller
{
    public function init()
    {
        $this->before[] = 'beforeAction';
    }

    public function beforeAction()
    {
        return false;
    }

    public function action()
    {
        throw new SampleControllerException("This action shouldn't be called!");
    }
}

class MockControllerResolver
{
    public function getCurrentController()
    {
        return new SampleController();
    }
}

class FrontControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerResolver = new MockControllerResolver();
        $this->_frontController->redirectHandler = $this->getMock('\Thulium\RedirectHandler', array('redirect'));
    }

    /**
     * @test
     */
    public function shouldRedirectToIndexWhenNoAction()
    {
        //given
        $config = Config::load()->getConfig('global');
        $_SERVER['REQUEST_URI'] = "{$config['prefix_system']}/crm";

        $this->_frontController->redirectHandler
            ->expects($this->once())
            ->method('redirect')
            ->with("{$config['prefix_system']}/crm/index");

        //when
        $this->_frontController->init();
    }

    /**
     * @test
     */
    public function shouldNotInvokeActionWhenBeforeFilterReturnFalse()
    {
        //given
        $actionIsInvoked = false;

        //when
        try {
            $this->get('/sample/action');
        } catch (SampleControllerException $exception) {
            $actionIsInvoked = true;
        }

        //then
        $this->assertEquals(false, $actionIsInvoked);
    }
}