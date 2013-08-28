<?php

use Thulium\Controller;

class SimpleTestController extends Controller
{
}

class SampleConfig
{
    public function getConfig()
    {
        $config['global']['prefix_system'] = '/path/to/panel/';
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
        Thulium\Config::registerConfig(new SampleConfig);
        $controller = new Controller();

        //when
        $controller->redirectOld('test.php', array('param1' => 'abc', 'param2' => 'def'));
        $redirect_location =  $controller->getRedirectLocation();

        //then
        $this->assertEquals('/path/to/panel/test.php?param1=abc&param2=def', $redirect_location);

    }
}