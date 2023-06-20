<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\PDOPreparedStatementExecutor;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PDOPreparedStatementExecutorTest extends TestCase
{
    private PDOStatement $pdoMock;
    private PDO $dbMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdoMock = Mock::mock(PDOStatement::class);
        $this->dbMock = Mock::mock(PDO::class);
        Mock::when($this->dbMock)->prepare('SELECT 1')->thenReturn($this->pdoMock);
        Mock::when($this->dbMock)->errorInfo()->thenReturn([1, 3, 'Preparation error']);
    }

    #[Test]
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
