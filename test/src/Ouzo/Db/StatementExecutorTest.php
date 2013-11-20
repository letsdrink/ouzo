<?php

namespace Ouzo\Db;

use Ouzo\Config;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock;

class StatementExecutorTest extends \PHPUnit_Framework_TestCase
{
    private $pdoMock;
    private $configSqlDialect;

    protected function setUp()
    {
        parent::setUp();
        $this->pdoMock = Mock::mock();
        Mock::when($this->pdoMock)->execute()->thenReturn(false);
        $this->configSqlDialect = Config::getValue('sql_dialect');
    }

    protected function tearDown()
    {
        Config::overrideProperty('sql_dialect')->with($this->configSqlDialect);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnExecutionError()
    {
        //given
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(array('HY000', '20102', 'Execution error'));
        $executor = StatementExecutor::prepare($this->pdoMock, 'SELECT 1');

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
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(array('HY000', 2003, 'Execution error'));
        $executor = StatementExecutor::prepare($this->pdoMock, 'SELECT 1');

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbConnectionException');
    }

    /**
     * @test
     */
    public function shouldThrowConnectionExceptionFromForPostgres()
    {
        //given
        Config::overrideProperty('sql_dialect')->with('\Ouzo\Db\Dialect\PostgresDialect');
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(array('57P01', 7, 'Execution error'));
        $executor = StatementExecutor::prepare($this->pdoMock, 'SELECT 1');

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbConnectionException');
    }
}
 