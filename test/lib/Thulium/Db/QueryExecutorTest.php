<?php

use Thulium\Db\EmptyQueryExecutor;
use Thulium\Db\Query;
use Thulium\Db\QueryExecutor;
use Thulium\Db;
use Thulium\Tests\DbTransactionalTestCase;

class QueryExecutorTest extends DbTransactionalTestCase
{

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionIfNoQueryObjectGiven()
    {
        QueryExecutor::prepare(Db::getInstance(), null);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionIfNoDbGiven()
    {
        // given
        $query = new Query();
        $query->table = 'table_name';

        // when
        QueryExecutor::prepare(null, $query);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldThrowExceptionIfNoTableNameGiven()
    {
        // given
        $query = new Query();

        // when
        QueryExecutor::prepare(Db::getInstance(), $query);
    }

    /**
     * @test
     */
    public function shouldReturnEmptyQueryExecutorForEmptyWhereValues()
    {
        // given
        $query = new Query();
        $query->table = 'table_name';
        $query->where = array('column' => array());

        // when
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        // then
        $this->assertTrue($executor instanceof EmptyQueryExecutor);
    }
}