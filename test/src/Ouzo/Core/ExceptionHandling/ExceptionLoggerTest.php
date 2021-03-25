<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

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
    }
}
