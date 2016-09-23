<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\ExceptionHandling\Error;
use Ouzo\UserException;

class ErrorTest extends PHPUnit_Framework_TestCase
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

    /**
     * @test
     */
    public function shouldGetErrorWithClassForDebug()
    {
        //given
        Config::overrideProperty('debug')->with(true);
        $userException = new UserException('Winter is coming!');

        //when
        $error = Error::forException($userException);

        //then
        $this->assertEquals('Ouzo\UserException: Winter is coming!', $error->message);
    }
}
