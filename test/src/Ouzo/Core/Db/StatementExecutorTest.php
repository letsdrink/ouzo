<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\Db\StatementExecutor;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;

class StatementExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PDOStatement
     */
    private $pdoMock;
    private $dbMock;

    protected function setUp()
    {
        parent::setUp();

        $this->pdoMock = Mock::mock();
        $this->dbMock = Mock::mock();
        Mock::when($this->pdoMock)->execute()->thenReturn(false);
        Mock::when($this->dbMock)->prepare('SELECT 1')->thenReturn($this->pdoMock);
        Mock::when($this->dbMock)->errorInfo()->thenReturn([1, 3, 'Preparation error']);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnExecutionError()
    {
        //given
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(['HY000', '20102', 'Execution error']);
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', [], []);

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbException');
    }

    /**
     * @test
     */
    public function shouldThrowConnectionExceptionFromForMySQL()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Db\Dialect\MySqlDialect');
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(['HY000', 2003, 'Execution error']);
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', [], []);

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbConnectionException');
        Config::revertProperty('sql_dialect');
    }

    /**
     * @test
     */
    public function shouldThrowConnectionExceptionFromForPostgres()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Db\Dialect\PostgresDialect');
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(['57P01', 7, 'Execution error']);
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', [], []);

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbConnectionException');
        Config::revertProperty('sql_dialect');
    }
}
