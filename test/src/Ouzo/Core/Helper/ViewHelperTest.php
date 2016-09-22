<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;

require_once ROOT_PATH . 'src/Ouzo/Core/Helper/ViewHelper.php';

class ViewHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldConstructUrlForController()
    {
        $url = url(array('controller' => 'users', 'action' => 'show', 'extraParams' => array('id' => 3, 'age' => 34)));

        $this->assertEquals(Config::getPrefixSystem() . '/users/show/id/3/age/34', $url);
    }

    /**
     * @test
     */
    public function shouldConstructUrlForAddress()
    {
        $url = url(array('string' => '/users/show'));

        $this->assertEquals(Config::getPrefixSystem() . '/users/show', $url);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionForInvalidArguments()
    {
        url(array('action' => 'show', 'extraParams' => array('id' => 3)));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionForEmptyParams()
    {
        url(array());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionForEmptyString()
    {
        url("");
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

    /**
     * @test
     */
    public function shouldRemoveStringFromHtmlFileTag()
    {
        //given
        $remove = '/js';

        //when
        $actual = addFile(array('type' => 'script', 'params' => array('url' => '/public/js/test.js')), $remove);

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<script type="text/javascript" src="/public/test.js?1234"></script>' . PHP_EOL, $actual);
    }

    /**
     * @test
     */
    public function shouldFormatDate()
    {
        //given
        $date = '2001-10-10 12:00:43';

        //when
        $formatted = formatDate($date);

        //then
        $this->assertEquals('2001-10-10', $formatted);
    }

    /**
     * @test
     */
    public function shouldFormatDateTime()
    {
        //given
        $date = '2001-10-10 12:00:43';

        //when
        $formatted = formatDateTime($date);

        //then
        $this->assertEquals('2001-10-10 12:00', $formatted);
    }

    /**
     * @test
     */
    public function shouldFormatDateTimeWithSeconds()
    {
        //given
        $date = '2001-10-10 12:00:43.107145';

        //when
        $formatted = formatDateTimeWithSeconds($date);

        //then
        $this->assertEquals('2001-10-10 12:00:43', $formatted);
    }

    /**
     * @test
     */
    public function shouldTranslate()
    {
        //given
        $key = 'product.description';

        //when
        $translated = t($key);

        //then
        $this->assertEquals('Product description', $translated);
    }

    /**
     * @test
     */
    public function shouldConvertObjectToString()
    {
        //given
        $obj = new stdClass();
        $obj->name = 'John';
        $obj->id = 1;

        //when
        $toString = toString($obj);

        //then
        /** @noinspection HtmlUnknownTag */
        $this->assertEquals('stdClass {<name> => "John", <id> => 1}', $toString);
    }

    /**
     * @test
     */
    public function shouldInflectWord()
    {
        //given
        $count = 1;

        //when
        $inflected = pluralise($count, array('plural' => 'clients', 'singular' => 'client'));

        //then
        $this->assertEquals('client', $inflected);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenFileInfoIsEmpty()
    {
        //when
        $file = addFile();

        //then
        $this->assertNull($file);
    }

    /**
     * @test
     */
    public function shouldReturnNullWhenTypeIsNullInHtmlFileTag()
    {
        //when
        $file = _getHtmlFileTag(null, null);

        //then
        $this->assertNull($file);
    }
}
