<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Db;
use Ouzo\DbException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\Tests\Mock\Mock;

class Sample
{
    public function callMethod(): string
    {
        return 'OK';
    }
}

class DbTest extends DbTransactionalTestCase
{
    /**
     * @test
     */
    public function shouldRunFunctionInTransaction()
    {
        //when
        $result = Db::getInstance()->runInTransaction(fn() => (new Sample())->callMethod());

        //then
        $this->assertEquals('OK', $result);
    }

    /**
     * @test
     */
    public function runInTransactionShouldInvokeBeginAndCommitOnSuccess()
    {
        // given
        Db::getInstance()->enableTransactions();
        $dbHandle = Mock::mock(PDO::class);

        $db = new Db(false);
        $db->dbHandle = $dbHandle;

        //when
        $result = $db->runInTransaction(fn() => (new Sample())->callMethod());

        //then
        $this->assertEquals('OK', $result);
        Mock::verify($dbHandle)->beginTransaction();
        Mock::verify($dbHandle)->commit();
        Mock::verify($dbHandle)->neverReceived()->rollbackTransaction();
    }

    /**
     * @test
     */
    public function runInTransactionShouldInvokeRollbackOnFailure()
    {
        // given
        Db::getInstance()->enableTransactions();
        $dbHandle = Mock::mock(PDO::class);

        $db = new Db(false);
        $db->dbHandle = $dbHandle;

        //when
        CatchException::when($db)->runInTransaction(fn() => throw new InvalidArgumentException());

        //then
        CatchException::assertThat()->isInstanceOf('InvalidArgumentException');
        Mock::verify($dbHandle)->beginTransaction();
        Mock::verify($dbHandle)->neverReceived()->commitTransaction();
        Mock::verify($dbHandle)->rollBack();
    }

    /**
     * @test
     */
    public function runThrowWhenPdoCommitFailed()
    {
        // given
        Db::getInstance()->enableTransactions();
        $dbHandle = Mock::mock(PDO::class);
        Mock::when($dbHandle)->commit()->thenReturn(false);
        Mock::when($dbHandle)->errorInfo()->thenReturn([
            '10A1',
            11,
            'Failed to commit - query timeout'
        ]);

        $db = new Db(false);
        $db->dbHandle = $dbHandle;

        //when
        CatchException::when($db)->runInTransaction(fn() => (new Sample())->callMethod());

        //then
        CatchException::assertThat()
            ->isInstanceOf(DbException::class)
            ->hasCode(11)
            ->hasMessage("Pdo method 'commit' failed. Message: 'Failed to commit - query timeout'. Code: '11'. SqlState code: '10A1'");
    }
}
