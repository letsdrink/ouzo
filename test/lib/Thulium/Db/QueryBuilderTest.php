<?php

use Thulium\Db\QueryBuilder;
use Thulium\Tests\DbTransactionalTestCase;

class QueryBuilderTest extends DbTransactionalTestCase
{

    /**
     * @test
     * @expectedException Exception
     */
    public function shouldThrowExceptionIfDbNotGiven()
    {
        new QueryBuilder('not db object');
    }

}