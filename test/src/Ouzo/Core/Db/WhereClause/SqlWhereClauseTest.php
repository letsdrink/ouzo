<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 7/19/16
 * Time: 2:59 PM
 */

namespace Ouzo\Db\WhereClause;


class SqlWhereClauseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldAcceptSingleValueAsParams()
    {
        // when
        $result = WhereClause::create('name = ?', 'bob');

        // then
        $this->assertEquals(array('bob'), $result->getParameters());
        $this->assertEquals('name = ?', $result->toSql());
    }

    /**
     * @test
     */
    public function shouldWrapSqlWithOrInParenthesis()
    {
        // when
        $result = WhereClause::create('name = ? OR name = ?', array('bob', 'john'));

        // then
        $this->assertEquals(array('bob', 'john'), $result->getParameters());
        $this->assertEquals('(name = ? OR name = ?)', $result->toSql());
    }
}