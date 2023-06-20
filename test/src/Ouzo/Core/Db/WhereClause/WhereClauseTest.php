<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Db\WhereClause;

use Ouzo\Db\Any;
use Ouzo\Restrictions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WhereClauseTest extends TestCase
{
    #[Test]
    public function shouldReturnEmptyWhereClauseForNull()
    {
        // when
        $result = WhereClause::create(null);

        // then
        $this->assertInstanceOf(EmptyWhereClause::class, $result);
    }

    #[Test]
    public function shouldReturnArrayWhereClauseForArray()
    {
        // when
        $result = WhereClause::create([]);

        // then
        $this->assertInstanceOf(ArrayWhereClause::class, $result);
    }

    #[Test]
    public function shouldReturnSqlWhereClauseForString()
    {
        // when
        $result = WhereClause::create('');

        // then
        $this->assertInstanceOf(SqlWhereClause::class, $result);
    }

    #[Test]
    public function shouldReturnGivenWhereClauseForWhereClauseInstance()
    {
        // given
        $whereClause = new EmptyWhereClause();

        // when
        $result = WhereClause::create($whereClause);

        // then
        $this->assertEquals($whereClause, $result);
    }

    #[Test]
    public function shouldReturnArrayWhereClauseForAny()
    {
        // when
        $result = WhereClause::create(Any::of(['a' => 'b', 'c' => 'd']));

        // then
        $this->assertInstanceOf(ArrayWhereClause::class, $result);
        $this->assertEquals(['0' => 'b', '1' => 'd'], $result->getParameters());
        $this->assertEquals('(a = ? OR c = ?)', $result->toSql());
    }

    #[Test]
    public function shouldReturnArrayWhereClauseForListOfRestrictions()
    {
        // when
        $result = WhereClause::create(['a' => [Restrictions::equalTo('b'), Restrictions::lessThan('c')]]);

        // then
        $this->assertInstanceOf(ArrayWhereClause::class, $result);
        $this->assertEquals(['0' => Restrictions::equalTo('b'), '1' => Restrictions::lessThan('c')], $result->getParameters());
        $this->assertEquals('(a = ? OR a < ?)', $result->toSql());
    }

    #[Test]
    public function shouldReturnArrayWhereClauseForAnyWithInAndWithoutRestriction()
    {
        // when
        $result = WhereClause::create(Any::of(['a' => ['b', 'c']]));

        // then
        $this->assertInstanceOf(ArrayWhereClause::class, $result);
        $this->assertEquals(['0' => 'b', '1' => 'c'], $result->getParameters());
        $this->assertEquals('a IN (?, ?)', $result->toSql());
    }

    #[Test]
    public function shouldAddParenthesisToListOfRestrictions()
    {

        // when
        $result = WhereClause::create([
            'a' => [Restrictions::equalTo('b'), Restrictions::lessThan('c')],
            'b' => 'd'
        ]);

        // then
        $this->assertInstanceOf(ArrayWhereClause::class, $result);
        $this->assertEquals('(a = ? OR a < ?) AND b = ?', $result->toSql());
    }

    #[Test]
    public function shouldNotAddParenthesisToSingleOfRestriction()
    {
        // when
        $result = WhereClause::create([
            'a' => Restrictions::equalTo('b')
        ]);

        // then
        $this->assertInstanceOf(ArrayWhereClause::class, $result);
        $this->assertEquals('a = ?', $result->toSql());
    }

    #[Test]
    public function shouldJoinConditionsWithOrForAnyOfAndAssociativeArray()
    {
        // when
        $result = Any::of(['name' => 'bob', 'age' => 12]);

        // then
        $this->assertEquals('(name = ? OR age = ?)', $result->toSql());
        $this->assertEquals(['0' => 'bob', '1' => 12], $result->getParameters());
    }

    #[Test]
    public function shouldJoinConditionsWithOrForAnyOfAndWhereClauses()
    {
        // when
        $result = Any::of([WhereClause::create('a = 1'), WhereClause::create('a = 2')]);

        // then
        $this->assertEquals('(a = 1 OR a = 2)', $result->toSql());
    }
}
