<?php
use Thulium\Config;

require_once ROOT_PATH . 'lib/Thulium/ViewHelper.php';

class ViewHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstructUrlForController()
    {
        $url = url(array('controller' => 'users', 'action' => 'show', 'extraParams' => array('id' => 3, 'age' => 34)));

        $this->assertEquals( \Thulium\Config::getPrefixSystem().'/users/show/id/3/age/34', $url );
    }

    /**
     * @test
     */
    public function shouldConstructUrlForAddress()
    {
        $url = url(array('string' => '/users/show'));

        $this->assertEquals( \Thulium\Config::getPrefixSystem().'/users/show', $url );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionForInvalidArguments()
    {
        url(array('action' => 'show', 'extraParams' => array('id' => 3)));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldThrowExceptionForEmptyParams()
    {
        url(array());
    }

    /**
     * @test
     */
    public function shouldReturnNullForNullDateInFormat()
    {
        //given
        $date = null;

        //when
        $formattedDate = formatDate($date);

        //then
        $this->assertNull($formattedDate);
    }

    /**
     * @test
     */
    public function shouldReturnHtmlToCssFile()
    {
        //given
        $prefixSystem = Config::load()->getConfig('global');

        //when
        $expected = '<link rel="stylesheet" href="' . $prefixSystem['prefix_system'] . '/public/css/style.css" type="text/css" />' . PHP_EOL;
        $actual = addFile(array('type' => 'link', 'params' => array('url' => '/public/css/style.css')));

        //then
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldReturnHtmlToJsFile()
    {
        //given
        $prefixSystem = Config::load()->getConfig('global');

        //when
        $expected = '<script type="text/javascript" src="' . $prefixSystem['prefix_system'] . '/public/js/test.js"></script>' . PHP_EOL;
        $actual = addFile(array('type' => 'script', 'params' => array('url' => '/public/js/test.js')));

        //then
        $this->assertEquals($expected, $actual);
    }
}