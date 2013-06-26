<?php

use Thulium\Db\PostgresQueryBuilder;
use Thulium\Tests\DbTransactionalTestCase;

class QueryBuilderTest extends DbTransactionalTestCase
{

    /**
     * @test
     * @expectedException Exception
     */
    public function shouldThrowExceptionIfDbNotGiven()
    {
        new PostgresQueryBuilder('not db object');
    }

}