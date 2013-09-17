<?php
namespace Ouzo;

use Exception;
use Ouzo\Config;
use Ouzo\Controller;
use Ouzo\Tests\ControllerTestCase;

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
        $this->_frontController->controllerResolver = new ControllerResolver('\\Ouzo\\');
        $this->_frontController->redirectHandler = $this->getMock('\Ouzo\RedirectHandler', array('redirect'));
    }

    /**
     * @test
     */
    public function shouldRenderIndexIfNoAction()
    {
        //given
        $config = Config::getValue('global');
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