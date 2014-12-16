<?php

use Ouzo\Db\PDOPreparedStatementExecutor;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;

class PDOPreparedStatementExecutorTest extends PHPUnit_Framework_TestCase
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
        Mock::when($this->dbMock)->prepare('SELECT 1')->thenReturn($this->pdoMock);
        Mock::when($this->dbMock)->errorInfo()->thenReturn(array(1, 3, 'Preparation error'));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnExecutionError()
    {
        //given
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(array('HY000', '20102', 'Execution error'));
        $executor = new PDOPreparedStatementExecutor();

        //when
        CatchException::when($executor)->createPDOStatement($this->dbMock, 'sql', array(), 'sql string');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbException');
    }
}
 