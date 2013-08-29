<?php
namespace Thulium;

use Exception;
use Thulium\Config;
use Thulium\Controller;
use Thulium\Tests\ControllerTestCase;

class SampleControllerException extends Exception
{
}

class SampleController extends Controller
{
    public function action()
    {
        echo "OUTPUT";
    }
    public function index()
    {
        $this->layout->renderAjax('index');
        $this->layout->unsetLayout();
    }
}

class FrontControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_frontController->controllerResolver = new ControllerResolver('\\Thulium\\');
        $this->_frontController->redirectHandler = $this->getMock('\Thulium\RedirectHandler', array('redirect'));
    }

    /**
     * @test
     */
    public function shouldRenderIndexIfNoAction()
    {
        //given
        $config = Config::load()->getConfig('global');
        $_SERVER['REQUEST_URI'] = "{$config['prefix_system']}/sample";

        //when
        $this->get('/sample');

        //then
        $this->assertRendersContent('index');
    }

    /**
     * @test
     */
    public function shouldNotDisplayOutput()
    {
        //when
        $this->get('/sample/action');

        //then
        $this->expectOutputString('');
    }
}