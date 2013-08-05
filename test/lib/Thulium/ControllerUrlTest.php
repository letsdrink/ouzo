<?php
use Thulium\Config;
use Thulium\ControllerUrl;

class ControllerUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateCorrectUrl()
    {
        //given
        $defaults = Config::load()->getConfig('global');

        //when
        $url = ControllerUrl::createUrl(array('controller' => 'users', 'action' => 'add'));

        //then
        $this->assertEquals($defaults['prefix_system'] . '/users/add', $url);
    }

    /**
     * @test
     */
    public function shouldCreateCorrectUrlFromString()
    {
        //given
        $defaults = Config::load()->getConfig('global');

        //when
        $url = ControllerUrl::createUrl(array('string' => '/users/add'));

        //then
        $this->assertEquals($defaults['prefix_system'] . '/users/add', $url);
    }

    /**
     * @test
     */
    public function shouldCreateCorrectUrlWithExtraParams()
    {
        //given
        $defaults = Config::load()->getConfig('global');

        //when
        $url = ControllerUrl::createUrl(array(
            'controller' => 'users',
            'action' => 'add',
            'extraParams' => array('id' => 5, 'name' => 'john')
        ));

        //then
        $this->assertEquals($defaults['prefix_system'] . '/users/add/id/5/name/john', $url);
    }
}