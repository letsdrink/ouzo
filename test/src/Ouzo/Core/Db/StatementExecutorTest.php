<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Db\Dialect\MySqlDialect;
use Ouzo\Db\Dialect\PostgresDialect;
use Ouzo\Db\StatementExecutor;
use Ouzo\DbConnectionException;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;

class StatementExecutorTest extends TestCase
{
    /**
     * @var PDOStatement
     */
    private $pdoMock;
    private $dbMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdoMock = Mock::mock();
        $this->dbMock = Mock::mock();
        Mock::when($this->pdoMock)->execute()->thenReturn(false);
        Mock::when($this->dbMock)->prepare('SELECT 1', [])->thenReturn($this->pdoMock);
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
        CatchException::assertThat()->isInstanceOf(DbException::class);
    }

    /**
     * @test
     */
    public function shouldThrowConnectionExceptionForMySQL()
    {
        //given
        Config::overrideProperty('sql_dialect')->with(MySqlDialect::class);
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(['HY000', 2003, 'Execution error']);
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', [], []);

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf(DbConnectionException::class);
        Config::revertProperty('sql_dialect');
    }

    /**
     * @test
     */
    public function shouldThrowConnectionExceptionForPostgres()
    {
        //given
        Config::overrideProperty('sql_dialect')->with(PostgresDialect::class);
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(['57P01', 7, 'Execution error']);
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', [], []);

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf(DbConnectionException::class);
        Config::revertProperty('sql_dialect');
    }
}
