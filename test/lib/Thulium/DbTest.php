<?php
use Thulium\Db;
use Thulium\Tests\DbTransactionalTestCase;

class Sample
{
    public function callMethod()
    {
        return 'OK';
    }
}

class DbTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldRunFunctionInTransaction()
    {
        //when
        $return = Db::getInstance()->runInTransaction(array(new Sample(), 'callMethod'));

        //then
        $this->assertEquals('OK', $return);
    }
}