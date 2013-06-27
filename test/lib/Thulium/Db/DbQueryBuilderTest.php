<?php

use Thulium\Db\DbQueryBuilder;
use Thulium\Tests\DbTransactionalTestCase;

class DbQueryBuilderTest extends DbTransactionalTestCase
{

    /**
     * @test
     * @expectedException Exception
     */
    public function shouldThrowExceptionIfDbNotGiven()
    {
        new DbQueryBuilder('not db object');
    }

}