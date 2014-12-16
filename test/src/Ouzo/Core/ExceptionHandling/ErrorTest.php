<?php

use Ouzo\Config;
use Ouzo\ExceptionHandling\Error;

class ErrorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldGetErrorForUserException()
    {
        //given
        Config::overrideProperty('debug')->with(false);
        $userException = new \Ouzo\UserException('Winter is coming!');

        //when
        $error = Error::forException($userException);

        //then
        $this->assertEquals('Winter is coming!', $error->message);
    }

}