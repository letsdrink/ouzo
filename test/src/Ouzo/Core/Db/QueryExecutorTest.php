<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\Model\Test\Product;
use Ouzo\Db;
use Ouzo\Db\EmptyQueryExecutor;
use Ouzo\Db\Query;
use Ouzo\Db\QueryExecutor;
use Ouzo\Tests\DbTransactionalTestCase;
use PHPUnit\Framework\Attributes\Test;

class QueryExecutorTest extends DbTransactionalTestCase
{
    #[Test]
    public function shouldThrowExceptionIfNoQueryObjectGiven()
    {
        $this->expectException(InvalidArgumentException::class);

        QueryExecutor::prepare(Db::getInstance(), null);
    }

    #[Test]
    public function shouldThrowExceptionIfNoDbGiven()
    {
        // given
        $this->expectException(InvalidArgumentException::class);
        $query = Query::select()->from('table_name');

        // when
        QueryExecutor::prepare(null, $query);
    }

    #[Test]
    public function shouldThrowExceptionIfNoTableNameGiven()
    {
        // given
        $this->expectException(InvalidArgumentException::class);
        $query = new Query();

        // when
        QueryExecutor::prepare(Db::getInstance(), $query);
    }

    #[Test]
    public function shouldReturnEmptyQueryExecutorForEmptyWhereValues()
    {
        // given
        $query = Query::select()->from('table_name')->where(['column' => []]);

        // when
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        // then
        $this->assertTrue($executor instanceof EmptyQueryExecutor);
    }

    #[Test]
    public function shouldReturnEmptyQueryExecutorForLimitZero()
    {
        //given
        $query = Query::select()->from('table_name')->where(['column' => 'first'])->limit(0);

        //when
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //then
        $this->assertTrue($executor instanceof EmptyQueryExecutor);

    }

    #[Test]
    public function shouldNotAddOffsetZero()
    {
        //given
        $query = Query::select()->from('table_name')->where(['column' => 'first'])->offset(0);

        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $executor->buildQuery();

        //then
        $this->assertEquals('SELECT * FROM table_name WHERE column = ?', $executor->getSql());
        $this->assertEquals(["first"], $executor->getBoundValues());
    }

    #[Test]
    public function shouldGenerateSqlForSubQueries()
    {
        //given
        $query = Query::select(['count(*)'])
            ->from(
                Query::select(['a', 'count(*) c'])->from('table')->groupBy('a')->where(['col' => 12]), 'sub'
            )->where(['c' => 123]);
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $executor->buildQuery();

        //then
        $this->assertEquals('SELECT count(*) FROM (SELECT a, count(*) c FROM table WHERE col = ? GROUP BY a) AS sub WHERE c = ?', $executor->getSql());
        $this->assertEquals([12, 123], $executor->getBoundValues());
    }

    #[Test]
    public function shouldHandleSubQueries()
    {
        //given
        Product::create(['name' => 'prod1', 'description' => 'd']);
        Product::create(['name' => 'prod1', 'description' => 'd']);
        Product::create(['name' => 'prod2', 'description' => 'd']);

        $query = Query::select(['count(*) AS c'])
            ->from(
                Query::select(['name', 'count(*) c'])->from('products')->groupBy('name')->where(['description' => 'd']), 'sub'
            )->where(['c' => 2]);
        $executor = QueryExecutor::prepare(Db::getInstance(), $query);

        //when
        $result = $executor->fetch();

        //then
        $this->assertEquals(['c' => 1], $result);
    }
}
