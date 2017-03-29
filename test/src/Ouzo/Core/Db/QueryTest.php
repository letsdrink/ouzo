<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db\Query;
use Ouzo\Db\QueryType;
use Ouzo\Db\WhereClause\ArrayWhereClause;
use Ouzo\Utilities\Arrays;

class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateSelectQuery()
    {
        // when
        $query = Query::select();

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertFalse($query->distinct);
    }

    /**
     * @test
     */
    public function shouldCreateSelectDistinctQuery()
    {
        // when
        $query = Query::selectDistinct();

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertTrue($query->distinct);
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
        $query = Query::select()->from('table')->where(['name' => 'bob'])->limit(5)->offset(10)->groupBy('group')->order(['name asc']);

        // then
        $this->assertEquals('table', $query->table);
        $this->assertEquals(new ArrayWhereClause(['name' => 'bob']), $query->whereClauses[0]);
        $this->assertEquals(5, $query->limit);
        $this->assertEquals(10, $query->offset);
        $this->assertEquals('group', $query->groupBy);
        $this->assertEquals(['name asc'], $query->order);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithJoin()
    {
        // when
        $query = Query::select()->join('table', 'id', 'other_id', 'tab');

        // then
        $this->assertCount(1, $query->joinClauses);
        $join = Arrays::first($query->joinClauses);
        $this->assertEquals('id', $join->joinColumn);
        $this->assertEquals('table', $join->joinTable);
        $this->assertEquals('other_id', $join->joinedColumn);
    }

    /**
     * @test
     */
    public function shouldCreateSelectQueryWithColumns()
    {
        // when
        $query = Query::select(['name', 'id']);

        // then
        $this->assertEquals(QueryType::$SELECT, $query->type);
        $this->assertEquals(['name', 'id'], $query->selectColumns);
    }

    /**
     * @group non-sqlite3
     * @test
     */
    public function shouldLockForUpdate()
    {
        // when
        $query = Query::select()->lockForUpdate();

        // then
        $this->assertTrue($query->lockForUpdate);
    }

    /**
     * @group non-sqlite3
     * @test
     */
    public function selectShouldNotLockForUpdateByDefault()
    {
        // when
        $query = Query::select();

        // then
        $this->assertFalse($query->lockForUpdate);
    }
}
