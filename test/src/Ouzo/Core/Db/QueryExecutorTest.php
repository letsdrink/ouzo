<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db\EmptyQueryExecutor;
use Ouzo\Db\Query;
use Ouzo\Db\QueryExecutor;
use Ouzo\Db;
use Ouzo\Tests\DbTransactionalTestCase;

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
        $query = Query::select()->from('table_name');

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
        $query = Query::select()->from('table_name')->where(array('column' => array()));

        // when
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        // then
        $this->assertTrue($executor instanceof EmptyQueryExecutor);
    }

    /**
     * @test
     */
    public function shouldHandleSubQueries()
    {
        //given
        $query = Query::select(array('count(*)'))
            ->from(
                Query::select(array('a', 'count(*) c'))->from('table')->groupBy('a')->where(array('col' => 12))
            )->where(array('c' => 123));
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $executor->_buildQuery();

        //then
        $this->assertEquals('SELECT count(*) FROM (SELECT a, count(*) c FROM table WHERE col = ? GROUP BY a) WHERE c = ?', $executor->getSql());
        $this->assertEquals(array(12, 123), $executor->getBoundValues());
    }
}
