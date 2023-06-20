<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;

require_once ROOT_PATH . 'src/Ouzo/Core/Helper/ViewHelper.php';

use PHPUnit\Framework\TestCase; 

class ViewHelperTest extends TestCase
{
    #[Test]
    public function shouldConstructUrlForController()
    {
        $url = url(['controller' => 'users', 'action' => 'show', 'extraParams' => ['id' => 3, 'age' => 34]]);

        $this->assertEquals(Config::getPrefixSystem() . '/users/show/id/3/age/34', $url);
    }

    #[Test]
    public function shouldConstructUrlForAddress()
    {
        $url = url(['string' => '/users/show']);

        $this->assertEquals(Config::getPrefixSystem() . '/users/show', $url);
    }

    #[Test]
    public function shouldThrowExceptionForInvalidArguments()
    {
        $this->expectException(InvalidArgumentException::class);

        url(['action' => 'show', 'extraParams' => ['id' => 3]]);
    }

    #[Test]
    public function shouldThrowExceptionForEmptyParams()
    {
        $this->expectException(InvalidArgumentException::class);

        url([]);
    }

    #[Test]
    public function shouldThrowExceptionForEmptyString()
    {
        $this->expectException(InvalidArgumentException::class);

        url("");
    }

    #[Test]
    public function shouldReturnNullForNullDateInFormat()
    {
        //given
        $date = null;

        //when
        $formattedDate = formatDate($date);

        //then
        $this->assertNull($formattedDate);
    }

    #[Test]
    public function shouldReturnHtmlToCssFile()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $expected = '<link type="text/css" href="' . $defaults['prefix_system'] . '/public/css/style.css?' . $defaults['suffix_cache'] . '" rel="stylesheet"/>' . PHP_EOL;
        $actual = addFile(['type' => 'link', 'params' => ['url' => '/public/css/style.css']]);

        //then
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function shouldReturnHtmlToJsFile()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $expected = '<script type="text/javascript" src="' . $defaults['prefix_system'] . '/public/js/test.js?' . $defaults['suffix_cache'] . '"></script>' . PHP_EOL;
        $actual = addFile(['type' => 'script', 'params' => ['url' => '/public/js/test.js']]);

        //then
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function shouldAddCacheSuffix()
    {
        //given
        $defaults = Config::getValue('global');

        //when
        $expected = '<script type="text/javascript" src="' . $defaults['prefix_system'] . '/public/js/test.js?' . $defaults['suffix_cache'] . '"></script>' . PHP_EOL;
        $actual = addFile(['type' => 'script', 'params' => ['url' => '/public/js/test.js']]);

        //then
        $this->assertEquals($expected, $actual);
    }

    #[Test]
    public function shouldRemoveStringFromHtmlFileTag()
    {
        //given
        $remove = '/js';

        //when
        $actual = addFile(['type' => 'script', 'params' => ['url' => '/public/js/test.js']], $remove);

        //then
        /** @noinspection HtmlUnknownTarget */
        $this->assertEquals('<script type="text/javascript" src="/public/test.js?1234"></script>' . PHP_EOL, $actual);
    }

    #[Test]
    public function shouldFormatDate()
    {
        //given
        $date = '2001-10-10 12:00:43';

        //when
        $formatted = formatDate($date);

        //then
        $this->assertEquals('2001-10-10', $formatted);
    }

    #[Test]
    public function shouldFormatDateTime()
    {
        //given
        $date = '2001-10-10 12:00:43';

        //when
        $formatted = formatDateTime($date);

        //then
        $this->assertEquals('2001-10-10 12:00', $formatted);
    }

    #[Test]
    public function shouldFormatDateTimeWithSeconds()
    {
        //given
        $date = '2001-10-10 12:00:43.107145';

        //when
        $formatted = formatDateTimeWithSeconds($date);

        //then
        $this->assertEquals('2001-10-10 12:00:43', $formatted);
    }

    #[Test]
    public function shouldTranslate()
    {
        //given
        $key = 'product.description';

        //when
        $translated = t($key);

        //then
        $this->assertEquals('Product description', $translated);
    }

    #[Test]
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

    #[Test]
    public function shouldInflectWord()
    {
        //given
        $count = 1;

        //when
        $inflected = pluralise($count, ['plural' => 'clients', 'singular' => 'client']);

        //then
        $this->assertEquals('client', $inflected);
    }

    #[Test]
    public function shouldReturnNullWhenFileInfoIsEmpty()
    {
        //when
        $file = addFile();

        //then
        $this->assertNull($file);
    }

    #[Test]
    public function shouldReturnNullWhenTypeIsNullInHtmlFileTag()
    {
        //when
        $file = Ouzo\Helper\ViewUtils::fileIncludeTag(null, null);

        //then
        $this->assertNull($file);
    }
}
