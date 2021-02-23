<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Http\AcceptHeaderParser;
use PHPUnit\Framework\TestCase;

class AcceptHeaderParserTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseAcceptHeader()
    {
        //given
        $accept = 'text/ *;q=0.3, text/html;q=0.7, */*;q=0.5';

        //when
        $parsed = AcceptHeaderParser::parse($accept);

        //then
        $this->assertEquals([
            'text/html' => 0.7,
            '*/*' => 0.5,
            'text/*' => 0.3
        ], $parsed);
    }

    /**
     * @test
     */
    public function shouldParseAcceptHeaderWithNoSubtype()
    {
        //given
        $accept = 'text/html, image/gif, image/jpeg, *; q=.2, */*; q=.2';

        //when
        $parsed = AcceptHeaderParser::parse($accept);

        //then
        $this->assertEquals([
            'text/html' => null,
            'image/gif' => null,
            'image/jpeg' => null,
            '*/*' => 0.2,
            '*' => 0.2
        ], $parsed);
    }

    /**
     * @test
     */
    public function shouldParseInvalidContentType()
    {
        //given
        $accept = 'invalid';

        //when
        $parsed = AcceptHeaderParser::parse($accept);

        //then
        $this->assertEquals([
            'invalid' => null
        ], $parsed);
    }

    /**
     * @test
     */
    public function shouldDecreaseWildcardsPriority()
    {
        //given
        $accept = 'text/plain, text/html, */*, text/*';

        //when
        $parsed = AcceptHeaderParser::parse($accept);

        //then
        $this->assertEquals(array_keys([
            'text/plain' => null,
            'text/html' => null,
            'text/*' => null,
            '*/*' => null
        ]), array_keys($parsed));
    }

    /**
     * @test
     */
    public function shouldReturnEmptyArrayForEmptyString()
    {
        //when
        $parsed = AcceptHeaderParser::parse('');

        //then
        $this->assertEmpty($parsed);
    }
}
