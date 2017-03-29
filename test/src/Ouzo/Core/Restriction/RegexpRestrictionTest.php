<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\Restrictions;

class RegexpRestrictionTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        Config::revertProperty('sql_dialect');
    }

    /**
     * @test
     */
    public function shouldCreateProperSqlForPostgres()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Db\Dialect\PostgresDialect');
        $restriction = Restrictions::regexp('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key ~ ?', $sql);
        $this->assertEquals(['value'], $restriction->getValues());
    }

    /**
     * @test
     */
    public function shouldCreateProperSqlForMysql()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Db\Dialect\MySqlDialect');
        $restriction = Restrictions::regexp('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key REGEXP ?', $sql);
        $this->assertEquals(['value'], $restriction->getValues());
    }

    /**
     * @test
     */
    public function shouldCreateProperSqlForSqlite()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Db\Dialect\Sqlite3Dialect');
        $restriction = Restrictions::regexp('value');

        //when
        $sql = $restriction->toSql('key');

        //then
        $this->assertEquals('key REGEXP ?', $sql);
        $this->assertEquals(['value'], $restriction->getValues());
    }
}
