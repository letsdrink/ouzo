<?php


use Ouzo\Config;
use Ouzo\Logger\LoggerInterface;
use Ouzo\Tests\Assert;
use Ouzo\Tests\StreamStub;
use Ouzo\Utilities\Clock;

class StdOutputLoggerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    protected function setUp()
    {
        parent::setUp();
        Clock::freeze('2014-01-01 11:11:11');
        StreamStub::register('test');
        $this->logger = new \Ouzo\Logger\StdOutputLogger('TEST', 'test');
    }

    protected function tearDown()
    {
        StreamStub::unregister();
        parent::tearDown();
    }


    /**
     * @test
     */
    public function shouldWriteErrorMessage()
    {
        //given
        //when
        $this->logger->error('My error log line with param %s and %s.', array(42, 'Zaphod'));

        //then
        $logContent = $this->_readStreamContent('test://stderr');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST Error: [ID: ] My error log line with param 42 and Zaphod.');
    }

    /**
     * @test
     */
    public function shouldWriteInfoMessage()
    {
        //given
        //when
        $this->logger->info('My info log line with param %s and %s.', array(42, 'Zaphod'));

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST Info: [ID: ] My info log line with param 42 and Zaphod.');
    }

    /**
     * @test
     */
    public function shouldWriteWarningMessage()
    {
        //given
        //when
        $this->logger->warning('My warning log line without params.');

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST Warning: [ID: ] My warning log line without params.');
    }

    /**
     * @test
     */
    public function shouldWriteFatalMessage()
    {
        //given
        //when
        $this->logger->fatal('My fatal log line without params.');

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST Fatal: [ID: ] My fatal log line without params.');
    }

    /**
     * @test
     */
    public function shouldWriteDebugMessageIfDebugIsOn()
    {
        //given
        Config::overrideProperty('debug')->with(true);

        //when
        $this->logger->debug('My debug log line without params.');

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST Debug: [ID: ] My debug log line without params.');
    }

    /**
     * @test
     */
    public function shouldIgnoreDebugMessageIfDebugIsOff()
    {
        //given
        Config::overrideProperty('debug')->with(false);

        //when
        $this->logger->debug('My debug log line without params.');

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->hasSize(0);
    }

    private function _readStreamContent($streamFile)
    {
        return file_get_contents($streamFile);
    }
}
 