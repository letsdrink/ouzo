<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
namespace Ouzo\Db\WhereClause;

use Ouzo\Db\Any;
use Ouzo\Restrictions;

class WhereClauseTest extends \PHPUnit_Framework_TestCase
{
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
        $this->assertEquals('(a = ? OR c = ?)', $result->toSql());
    }

    /**
     * @test
     */
    public function shouldReturnArrayWhereClauseForListOfRestrictions()
    {
        // when
        $result = WhereClause::create(array('a' => array(Restrictions::equalTo('b'), Restrictions::lessThan('c'))));

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\ArrayWhereClause', $result);
        $this->assertEquals(array('0' => Restrictions::equalTo('b'), '1' => Restrictions::lessThan('c')), $result->getParameters());
        $this->assertEquals('(a = ? OR a < ?)', $result->toSql());
    }

    /**
     * @test
     */
    public function shouldReturnArrayWhereClauseForAnyWithInAndWithoutRestriction()
    {
        // when
        $result = WhereClause::create(Any::of(array('a' => array('b', 'c'))));

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\ArrayWhereClause', $result);
        $this->assertEquals(array('0' => 'b', '1' => 'c'), $result->getParameters());
        $this->assertEquals('a IN (?, ?)', $result->toSql());
    }

    /**
     * @test
     */
    public function shouldAddParenthesisToListOfRestrictions()
    {

        // when
        $result = WhereClause::create(array(
            'a' => array(Restrictions::equalTo('b'), Restrictions::lessThan('c')),
            'b' => 'd'
        ));

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\ArrayWhereClause', $result);
        $this->assertEquals('(a = ? OR a < ?) AND b = ?', $result->toSql());
    }

    /**
     * @test
     */
    public function shouldNotAddParenthesisToSingleOfRestriction()
    {
        // when
        $result = WhereClause::create(array(
            'a' => Restrictions::equalTo('b')
        ));

        // then
        $this->assertInstanceOf('\Ouzo\Db\WhereClause\ArrayWhereClause', $result);
        $this->assertEquals('a = ?', $result->toSql());
    }

    /**
     * @test
     */
    public function shouldJoinConditionsWithOrForAnyOfAndAssociativeArray()
    {
        // when
        $result = Any::of(['name' => 'bob', 'age' => 12]);

        // then
        $this->assertEquals('(name = ? OR age = ?)', $result->toSql());
        $this->assertEquals(array('0' => 'bob', '1' => 12), $result->getParameters());
    }

    /**
     * @test
     */
    public function shouldJoinConditionsWithOrForAnyOfAndWhereClauses()
    {
        // when
        $result = Any::of([WhereClause::create('a = 1'), WhereClause::create('a = 2')]);

        // then
        $this->assertEquals('(a = 1 OR a = 2)', $result->toSql());
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
