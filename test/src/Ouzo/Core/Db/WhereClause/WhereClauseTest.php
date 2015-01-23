<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;


use Ouzo\Db\Any;

class WhereClauseTest extends \PHPUnit_Framework_TestCase {


    /**
     * @test
     */
    public function shouldReturnEmptyWhereClauseForNull()
    {
        // when
        $result = WhereClause::create(null);

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\EmptyWhereClause', $result);
    }

    /**
     * @test
     */
    public function shouldReturnArrayWhereClauseForArray()
    {
        // when
        $result = WhereClause::create(array());

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\ArrayWhereClause', $result);
    }

    /**
     * @test
     */
    public function shouldReturnSqlWhereClauseForString()
    {
        // when
        $result = WhereClause::create('');

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\SqlWhereClause', $result);
    }

    /**
     * @test
     */
    public function shouldReturnGivenWhereClauseForWhereClauseInstance()
    {
        // given
        $whereClause = new EmptyWhereClause();

        // when
        $result = WhereClause::create($whereClause);

        // then
        $this->assertEquals($whereClause, $result);
    }

    /**
     * @test
     */
    public function shouldReturnArrayWhereClauseForAny()
    {
        // when
        $result = WhereClause::create(Any::of(array('a' => 'b', 'c' => 'd')));

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\ArrayWhereClause', $result);
        $this->assertEquals(array('0' => 'b', '1' => 'd'), $result->getParameters());
        $this->assertEquals('a = ? OR c = ?', $result->toSql());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function shouldFailForNotSupportedParameter()
    {
        WhereClause::create(1);
    }
}
