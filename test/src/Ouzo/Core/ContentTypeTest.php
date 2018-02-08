<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\ContentType;
use PHPUnit\Framework\TestCase;

class ContentTypeTest extends TestCase
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
