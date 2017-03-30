<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Db\EmulatedPDOPreparedStatementExecutor;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;

class EmulatedPDOPreparedStatementExecutorTest extends PHPUnit_Framework_TestCase
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
        Mock::when($this->pdoMock)->query(Mock::anyArgList())->thenReturn(false);
        Mock::when($this->dbMock)->errorInfo()->thenReturn([1, 3, 'Preparation error']);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnExecutionError()
    {
        //given
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(['HY000', '20102', 'Execution error']);
        $executor = new EmulatedPDOPreparedStatementExecutor();

        //when
        CatchException::when($executor)->createPDOStatement($this->dbMock, 'sql', [], 'sql string');

        //then
        CatchException::assertThat()->isInstanceOf(DbException::class);
    }
}
