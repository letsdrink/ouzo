<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Application\Model\Test\Product;
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
    public function shouldReturnEmptyQueryExecutorForLimitZero()
    {
        //given
        $query = Query::select()->from('table_name')->where(array('column' => 'first'))->limit(0);

        //when
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //then
        $this->assertTrue($executor instanceof EmptyQueryExecutor);

    }

    /**
     * @test
     */
    public function shouldNotAddOffsetZero()
    {
        //given
        $query = Query::select()->from('table_name')->where(array('column' => 'first'))->offset(0);

        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $executor->_buildQuery();

        //then
        $this->assertEquals('SELECT * FROM table_name WHERE column = ?', $executor->getSql());
        $this->assertEquals(array("first"), $executor->getBoundValues());
    }

    /**
     * @test
     */
    public function shouldGenerateSqlForSubQueries()
    {
        //given
        $query = Query::select(array('count(*)'))
            ->from(
                Query::select(array('a', 'count(*) c'))->from('table')->groupBy('a')->where(array('col' => 12)), 'sub'
            )->where(array('c' => 123));
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $executor->_buildQuery();

        //then
        $this->assertEquals('SELECT count(*) FROM (SELECT a, count(*) c FROM table WHERE col = ? GROUP BY a) AS sub WHERE c = ?', $executor->getSql());
        $this->assertEquals(array(12, 123), $executor->getBoundValues());
    }

    /**
     * @test
     */
    public function shouldHandleSubQueries()
    {
        //given
        Product::create(array('name' => 'prod1', 'description' => 'd'));
        Product::create(array('name' => 'prod1', 'description' => 'd'));
        Product::create(array('name' => 'prod2', 'description' => 'd'));

        $query = Query::select(array('count(*) AS c'))
            ->from(
                Query::select(array('name', 'count(*) c'))->from('products')->groupBy('name')->where(array('description' => 'd')), 'sub'
            )->where(array('c' => 2));
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $result = $executor->fetch();

        //then
        $this->assertEquals(array('c' => 1), $result);
    }
}
