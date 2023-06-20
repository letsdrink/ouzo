<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db\EmulatedPDOPreparedStatementExecutor;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class EmulatedPDOPreparedStatementExecutorTest extends TestCase
{
    private PDOStatement $pdoMock;
    private PDO $dbMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdoMock = Mock::mock(PDOStatement::class);
        $this->dbMock = Mock::mock(PDO::class);
        Mock::when($this->pdoMock)->query(Mock::anyArgList())->thenReturn(false);
        Mock::when($this->dbMock)->errorInfo()->thenReturn([1, 3, 'Preparation error']);
    }

    #[Test]
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
