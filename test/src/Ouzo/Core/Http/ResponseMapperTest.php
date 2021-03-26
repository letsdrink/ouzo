<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Http\ResponseMapper;
use PHPUnit\Framework\TestCase;

class ResponseMapperTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnResponse()
    {
        //when
        $response = ResponseMapper::getMessage(404);

        //then
        $this->assertEquals('404 Not Found', $response);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultResponseWhenNotFoundCode()
    {
        //when
        $response = ResponseMapper::getMessage(999);

        //then
        $this->assertEquals('500 Internal Server Error', $response);
    }

    /**
     * @test
     */
    public function shouldReturnResponseWithProtocol()
    {
        //when
        $response = ResponseMapper::getMessageWithHttpProtocol(404);

        //then
        $this->assertEquals('HTTP/1.1 404 Not Found', $response);
    }
}
