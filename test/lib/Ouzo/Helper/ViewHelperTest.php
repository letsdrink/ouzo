<?php
use Ouzo\Config;

require_once ROOT_PATH . 'lib/Ouzo/Helper/ViewHelper.php';

class ViewHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstructUrlForController()
    {
        $url = url(array('controller' => 'users', 'action' => 'show', 'extraParams' => array('id' => 3, 'age' => 34)));

        $this->assertEquals(\Ouzo\Config::getPrefixSystem() . '/users/show/id/3/age/34', $url);
    }

    /**
     * @test
     */
    public function shouldConstructUrlForAddress()
    {
        $url = url(array('string' => '/users/show'));

        $this->assertEquals(\Ouzo\Config::getPrefixSystem() . '/users/show', $url);
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
        $defaults = Config::getValue('global');

        //when
        $expected = '<link rel="stylesheet" href="' . $defaults['prefix_system'] . '/public/css/style.css?' . $defaults['suffix_cache'] . '" type="text/css" />' . PHP_EOL;
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
        $defaults = Config::getValue('global');

        //when
        $expected = '<script type="text/javascript" src="' . $defaults['prefix_system'] . '/public/js/test.js?' . $defaults['suffix_cache'] . '"></script>' . PHP_EOL;
        $actual = addFile(array('type' => 'script', 'params' => array('url' => '/public/js/test.js')));

        //then
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function shouldAddCacheSuffix()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $expected = '<script type="text/javascript" src="' . $defaults['prefix_system'] . '/public/js/test.js?' . $defaults['suffix_cache'] . '"></script>' . PHP_EOL;
        $actual = addFile(array('type' => 'script', 'params' => array('url' => '/public/js/test.js')));

        //then
        $this->assertEquals($expected, $actual);
    }
}