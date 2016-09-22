<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Config;
use Ouzo\Logger\LoggerInterface;
use Ouzo\Logger\StdOutputLogger;
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
        $this->logger = new StdOutputLogger('TEST', 'default', 'test');
    }

    protected function tearDown()
    {
        Config::clearProperty('logger', 'default', 'minimal_levels');
        StreamStub::unregister();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldWriteErrorMessage()
    {
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
        //when
        $this->logger->info('My info log line with param %s and %s.', array(42, 'Zaphod'));

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST Info: [ID: ] My info log line with param 42 and Zaphod.');
    }

    /**
     * @test
     */
    public function shouldNotWriteInfoMessageIfMinimalLevelIsSetToWarning()
    {
        //given
        Config::overrideProperty('logger', 'default', 'minimal_levels')->with(array('TEST' => LOG_WARNING));
        $this->logger = new StdOutputLogger('TEST', 'default', 'test');

        //when
        $this->logger->info('My info log line with param %s and %s.', array(42, 'Zaphod'));

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->isEmpty();
    }

    /**
     * @test
     */
    public function shouldWriteInfoMessageIfMinimalLevelIsSetToInfo()
    {
        //given
        Config::overrideProperty('logger', 'default', 'minimal_levels')->with(array('TEST' => LOG_INFO));
        $this->logger = new StdOutputLogger('TEST', 'default', 'test');

        //when
        $this->logger->info('My info log line with param %s and %s.', array(42, 'Zaphod'));

        //then
        $logContent = $this->_readStreamContent('test://stdout');
        Assert::thatString($logContent)->isNotEmpty();
    }

    /**
     * @test
     */
    public function shouldWriteWarningMessage()
    {
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
