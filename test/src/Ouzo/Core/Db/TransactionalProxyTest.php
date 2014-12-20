<?php
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
