<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\PDOPreparedStatementExecutor;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;

class PDOPreparedStatementExecutorTest extends TestCase
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
        $executor = new PDOPreparedStatementExecutor();

        //when
        CatchException::when($executor)->createPDOStatement($this->dbMock, 'sql', [], 'sql string');

        //then
        CatchException::assertThat()->isInstanceOf(DbException::class);
    }
}
