<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Logger\Backtrace;
use PHPUnit\Framework\TestCase;

class BacktraceTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnCallingClassWithEmptyIgnoreList()
    {
        //when
        $callingClass = Backtrace::getCallingClass([]);

        //then
        $this->assertStringStartsWith('BacktraceTest:', $callingClass);
    }

    /**
     * @test
     */
    public function shouldReturnCallingClassWithDefaultIgnoreList()
    {
        //when
        $callingClass = Backtrace::getCallingClass();

        //then
        $this->assertStringStartsWith('BacktraceTest:', $callingClass);
    }

    /**
     * @test
     */
    public function shouldReturnCallingClassWithCustomIgnoreList()
    {
        //when
        $callingClass = Backtrace::getCallingClass(['BacktraceTest']);

        //then
        $this->assertStringStartsWith('PHPUnit\Framework\TestCase:', $callingClass);
    }
}
