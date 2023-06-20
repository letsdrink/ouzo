<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\ExceptionHandling;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ExceptionLoggerTest extends TestCase
{
    #[Test]
    public function shouldNotWritePasswordToLog()
    {
        // when
        $result = ExceptionLogger::sanitize(['login' => 'xxx', 'password' => 'yyy']);

        // then
        $this->assertEquals('[<login> => "xxx", <password> => "***"]', $result);
    }

    #[Test]
    public function shouldLogSource()
    {
        //given
        $exceptionData = new OuzoExceptionData(500, [new Error(1, "Internal error")], new StackTrace("/var/www/Application/Model/User.php", 11));
        $logger = new ExceptionLogger($exceptionData);

        //when
        $message = $logger->getMessage();

        //then
        $this->assertStringContainsString(" [source: /var/www/Application/Model/User.php:11]", $message);
    }
}
