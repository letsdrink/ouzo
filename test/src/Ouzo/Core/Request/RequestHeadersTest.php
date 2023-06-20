<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\ModelDefinition;
use Ouzo\Request\RequestHeaders;
use Ouzo\Tests\Assert;

use PHPUnit\Framework\TestCase; 

class RequestHeadersTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        RequestHeaders::clearCache();
        unset($_SERVER['HTTP_ACCEPT']);
        unset($_SERVER['HTTP_ACCEPT_CHARSET']);
        unset($_SERVER['HTTP_ACCEPT_ENCODING']);
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        unset($_SERVER['HTTP_CACHE_CONTROL']);
        unset($_SERVER['HTTP_CONNECTION']);
        unset($_SERVER['HTTP_COOKIE']);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['HTTP_USER_AGENT']);
        unset($_SERVER['HTTP_CLIENT_IP']);
        unset($_SERVER['REMOTE_ADDR']);
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        unset($_SERVER['HTTP_REFERER']);
        unset($_SERVER['HTTP_X_CSRFTOKEN']);
    }

    #[Test]
    public function shouldGetArrayOfAllHeaders()
    {
        //given
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
        $_SERVER['HTTP_ACCEPT_CHARSET'] = 'UTF-8,*;q=0.5';
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip,deflate,sdch';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-US,en;q=0.8';
        $_SERVER['HTTP_CACHE_CONTROL'] = 'max-age=0';
        $_SERVER['HTTP_CONNECTION'] = 'keep-alive';
        $_SERVER['HTTP_COOKIE'] = '__utmz=179618234.1309856897.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utma=179618234.703966342.1309856897.1309856897.1309856897.1';
        $_SERVER['HTTP_HOST'] = 'www.yoursite.com';
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.30 (KHTML, like Gecko) Ubuntu/11.04 Chromium/12.0.742.112 Chrome/12.0.742.112 Safari/534.30';

        //when
        $all = RequestHeaders::all();

        //then
        Assert::thatArray($all)->hasSize(9)
            ->containsKeyAndValue([
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Charset' => 'UTF-8,*;q=0.5',
                'Accept-Encoding' => 'gzip,deflate,sdch',
                'Accept-Language' => 'en-US,en;q=0.8',
                'Cache-Control' => 'max-age=0',
                'Connection' => 'keep-alive',
                'Cookie' => '__utmz=179618234.1309856897.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utma=179618234.703966342.1309856897.1309856897.1309856897.1',
                'Host' => 'www.yoursite.com',
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/534.30 (KHTML, like Gecko) Ubuntu/11.04 Chromium/12.0.742.112 Chrome/12.0.742.112 Safari/534.30'
            ]);
    }

    #[Test]
    public function shouldReturnIpFromCLIENT_IP()
    {
        //given
        $_SERVER['HTTP_CLIENT_IP'] = '10.170.12.51';

        //when
        $ip = RequestHeaders::ip();

        //then
        $this->assertEquals('10.170.12.51', $ip);
    }

    #[Test]
    public function shouldReturnIpFromX_FORWARDED_FOR()
    {
        //given
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.170.12.52';

        //when
        $ip = RequestHeaders::ip();

        //then
        $this->assertEquals('10.170.12.52', $ip);
    }

    #[Test]
    public function shouldReturnIpFromREMOTE_ADDR()
    {
        //given
        $_SERVER['REMOTE_ADDR'] = '10.170.12.53';

        //when
        $ip = RequestHeaders::ip();

        //then
        $this->assertEquals('10.170.12.53', $ip);
    }
}
