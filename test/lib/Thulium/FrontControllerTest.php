<?php
use Thulium\Config;
use Thulium\Controller;
use Thulium\Tests\ControllerTestCase;
use Thulium\Tests\MockOutoutDisplayer;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public function action()
    {
        echo "OUTPUT";
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
    public function shouldNoDisplayOutput()
    {
        //when
        $this->get('/sample/action');

        //then
        $this->expectOutputString('');
    }
}