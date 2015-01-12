<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db;

class TransactionalProxyTest extends PHPUnit_Framework_TestCase
{
    private $transactionStatus;

    /**
     * @test
     */
    public function shouldRunInTransaction()
    {
        //when
        Db::transactional($this)->method();

        //then
        $this->assertTrue($this->transactionStatus);
    }

    public function method()
    {
        $startedTransaction = Db::getInstance()->_startedTransaction;
        $this->transactionStatus = $startedTransaction;
    }
}
