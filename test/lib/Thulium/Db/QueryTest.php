<?php

use Thulium\Db\Query;
use Thulium\Db\QueryType;

class QueryTest extends PHPUnit_Framework_TestCase {

    /**
     * @test
     */
    public function shouldCreateSelectQuery()
    {
        // when
        $query = Query::select();

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
    }

    /**
     * @test
     */
    public function shouldCreateSelectCountQuery()
    {
        // when
        $query = Query::count();

        // then
        $this->assertEquals(QueryType::$COUNT, $query->type);
    }

    /**
     * @test
     */
    public function shouldCreateDeleteQuery()
    {
        // when
        $query = Query::delete();

        // then
        $this->assertEquals(QueryType::$DELETE, $query->type);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithWhereOrderLimitOffset()
    {
        // when
        $query = Query::select()->from('table')->where(array('name' => 'bob'))->limit(5)->offset(10)->order(array('name asc'));

        // then
        $this->assertEquals('table', $query->table);
        $this->assertEquals(array('name' => 'bob'), $query->whereClause->where);
        $this->assertEquals(5, $query->limit);
        $this->assertEquals(10, $query->offset);
        $this->assertEquals(array('name asc'), $query->order);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithJoin()
    {
        // when
        $query = Query::select()->join('table', 'id', 'other_id');

        // then
        $this->assertEquals('id', $query->joinKey);
        $this->assertEquals('table', $query->joinTable);
        $this->assertEquals('other_id', $query->idName);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithColumns()
    {
        // when
        $query = Query::select(array('name', 'id'));

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertEquals(array('name', 'id'), $query->selectColumns);
    }

}