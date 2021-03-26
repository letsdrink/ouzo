<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\ExceptionHandling\Error;
use Ouzo\UserException;

use PHPUnit\Framework\TestCase; 

class ErrorTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetErrorForUserException()
    {
        //given
        Config::overrideProperty('debug')->with(false);
        $userException = new UserException('Winter is coming!');

        //when
        $error = Error::forException($userException);

        //then
        $this->assertEquals('Winter is coming!', $error->message);
    }

}
