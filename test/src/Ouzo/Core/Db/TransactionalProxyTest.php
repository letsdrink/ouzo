<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db;

class TransactionalProxyTest extends PHPUnit_Framework_TestCase
{
    public $transactionStatus;

    /**
     * @test
     */
    public function shouldRunMethodInTransaction()
    {
        //when
        Db::transactional($this)->method();

        //then
        $this->assertTrue($this->transactionStatus);
    }

    /**
     * @test
     */
    public function shouldRunFunctionInTransaction()
    {
        //given
        $test = $this;
        $transactionalFunction = Db::transactional(function ($param1, $param2) use ($test) {
            $test->method();
            $test->assertEquals(1, $param1);
            $test->assertEquals(2, $param2);
        });

        //when
        $transactionalFunction(1, 2);

        //then
        $this->assertTrue($this->transactionStatus);
    }

    public function method()
    {
        $startedTransaction = Db::getInstance()->_startedTransaction;
        $this->transactionStatus = $startedTransaction;
    }
}
