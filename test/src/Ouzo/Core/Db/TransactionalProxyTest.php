<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db;

use PHPUnit\Framework\TestCase;
class TransactionalProxyTest extends TestCase
{
    public bool $transactionStatus;

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
        $transactionalFunction = Db::transactional(function ($param1, $param2) {
            $this->method();
            $this->assertEquals(1, $param1);
            $this->assertEquals(2, $param2);
        });

        //when
        $transactionalFunction(1, 2);

        //then
        $this->assertTrue($this->transactionStatus);
    }

    public function method()
    {
        $startedTransaction = Db::getInstance()->startedTransaction;
        $this->transactionStatus = $startedTransaction;
    }
}
