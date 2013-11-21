<?php

namespace Ouzo\Db;

use Ouzo\Config;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;

class StatementExecutorTest extends \PHPUnit_Framework_TestCase
{
    private $pdoMock;
    private $dbMock;
    private $configSqlDialect;

    protected function setUp()
    {
        parent::setUp();
        $this->pdoMock = Mock::mock();
        $this->dbMock = Mock::mock();
        Mock::when($this->pdoMock)->execute()->thenReturn(false);
        Mock::when($this->dbMock)->prepare('SELECT 1')->thenReturn($this->pdoMock);
        Mock::when($this->dbMock)->errorInfo()->thenReturn(array(1, 3, 'Preparation error'));
        $this->configSqlDialect = Config::getValue('sql_dialect');
    }

    protected function tearDown()
    {
        Config::revertProperty('sql_dialect');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnExecutionError()
    {
        //given
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(array('HY000', '20102', 'Execution error'));
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', array());

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
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', array());

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
        $executor = StatementExecutor::prepare($this->dbMock, 'SELECT 1', array());

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbConnectionException');
    }
}
 