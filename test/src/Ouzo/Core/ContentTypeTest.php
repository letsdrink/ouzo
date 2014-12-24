<?php

use Ouzo\ContentType;

class ContentTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldParseContentType()
    {
        //given
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        //when
        $value = ContentType::value();

        //then
        $this->assertEquals('application/json', $value);
    }

    /**
     * @test
     */
    public function shouldSetContentType()
    {
        //given
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        //when
        ContentType::set('text/plain');

        //then
        $this->assertEquals('text/plain', ContentType::value());
    }
}
