<?php

use Thulium\Db\DbQueryBuilder;
use Thulium\Db\QueryExecutor;
use Thulium\Tests\DbTransactionalTestCase;

class QueryExecutorTest extends DbTransactionalTestCase
{

    /**
     * @test
     * @expectedException Exception
     */
    public function shouldThrowExceptionIfDbNotGiven()
    {
        QueryExecutor::prepare(null, null);
    }

}