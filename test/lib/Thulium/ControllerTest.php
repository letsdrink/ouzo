<?php

use Thulium\Controller;
use Thulium\Config;

class SimpleTestController extends Controller
{
}

class SampleConfigController
{
    public function getConfig()
    {
        $config['global']['prefix_system'] = '/panel/panel2.0';
        $config['global']['action'] = 'index';
        return $config;
    }
}

class ControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnClassNameInUnderscoreAsDefaultTab()
    {
        //when
        $tab = SimpleTestController::getTab();

        //then
        $this->assertEquals('simple_test', $tab);
    }

    /**
     * @test
     */
    public function shouldRedirectToOldPanel()
    {
        //given
        Config::registerConfig(new SampleConfigController);
        $controller = new Controller();

        //when
        $controller->redirectOld('index.php', array('param1' => 'abc', 'param2' => 'def'));
        $redirect_location =  $controller->getRedirectLocation();

        //then
        $this->assertEquals('/panel/index.php?param1=abc&param2=def', $redirect_location);
    }
}