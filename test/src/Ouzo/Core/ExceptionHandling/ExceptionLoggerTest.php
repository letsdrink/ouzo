<?php

namespace Ouzo\ExceptionHandling;


use PHPUnit\Framework\TestCase;

class ExceptionLoggerTest extends TestCase
{

    /**
     * @test
     */
    public function shouldNotWritePasswordToLog()
    {
        // when
        $result = ExceptionLogger::sanitize(['login' => 'xxx', 'password' => 'yyy']);

        // then
        $this->assertEquals('[<login> => "xxx", <password> => "***"]', $result);

        var_dump((new \Exception())->getTraceAsString());
    }
}
