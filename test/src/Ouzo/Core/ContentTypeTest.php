<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\ContentType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ContentTypeTest extends TestCase
{
    #[Test]
    public function shouldParseContentType()
    {
        //given
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        //when
        $value = ContentType::value();

        //then
        $this->assertEquals('application/json', $value);
    }

    #[Test]
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
