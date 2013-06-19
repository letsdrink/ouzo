<?php
use Thulium\Config;

class FrontControllerTest extends PHPUnit_Framework_TestCase
{
    private $_redirectHandler;

    public function setUp()
    {
        $this->_redirectHandler = $this->getMock('\Thulium\RedirectHandler', array('redirect'));
    }

    /**
     * @test
     */
    public function shouldRedirectToIndexWhenNoAction()
    {
        //given
        $config = Config::load()->getConfig('global');
        $_SERVER['REQUEST_URI'] = "{$config['prefix_system']}/crm";
        $frontConroller = new \Thulium\FrontController();

        $frontConroller->redirectHandler = $this->_redirectHandler;

        $this->_redirectHandler
            ->expects($this->once())
            ->method('redirect')
            ->with("{$config['prefix_system']}/crm/index");

        //when
        $frontConroller->init();
    }
}