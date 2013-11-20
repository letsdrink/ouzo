<?php


namespace Ouzo\Db;

use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock;

class StatementExecutorTest extends \PHPUnit_Framework_TestCase
{
    private $pdoMock;

    protected function setUp()
    {
        parent::setUp();
        $this->pdoMock = Mock::mock();
        Mock::when($this->pdoMock)->execute()->thenReturn(false);
        Mock::when($this->pdoMock)->errorInfo()->thenReturn(array('HY000', '20102', 'Execution error'));
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnExecutionError()
    {
        //given
        $executor = StatementExecutor::prepare($this->pdoMock, 'SELECT 1');

        //when
        CatchException::when($executor)->execute();

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\DbException');
    }
}
 