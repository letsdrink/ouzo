<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
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
        //given
        $code = 404;

        //when
        $response = ResponseMapper::getMessage($code);

        //then
        $this->assertEquals('404 Not Found', $response);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultResponseWhenNotFoundCode()
    {
        //given
        $code = 999;

        //when
        $response = ResponseMapper::getMessage($code);

        //then
        $this->assertEquals('500 Internal Server Error', $response);
    }

    /**
     * @test
     */
    public function shouldReturnResponseWithProtocol()
    {
        //given
        $code = 404;

        //when
        $response = ResponseMapper::getMessageWithHttpProtocol($code);

        //then
        $this->assertEquals('HTTP/1.1 404 Not Found', $response);
    }
}
