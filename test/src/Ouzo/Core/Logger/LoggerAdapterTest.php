<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Logger\LoggerAdapter;
use Ouzo\Logger\StdOutputLogger;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use Ouzo\Tests\StreamStub;
use Ouzo\Utilities\Clock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LoggerAdapterTest extends TestCase
{
    private LoggerAdapter $logger;


    protected function setUp(): void
    {
        parent::setUp();
        Clock::freeze('2014-01-01 11:11:11');
        StreamStub::register('test');
        $this->logger = new LoggerAdapter('TEST', 'default', new StdOutputLogger('TEST', 'default', 'test'));
    }

    protected function tearDown(): void
    {
        Config::clearProperty('logger', 'default', 'minimal_levels');
        StreamStub::unregister();
        parent::tearDown();
    }

    #[Test]
    public function shouldSendContextWhenLoggingMessage()
    {
        //given
        $logger = new class() extends AbstractLogger {
            public ?string $currentMessage = null;
            public array $currentContext = [];

            public function log($level, $message, array $context = []): void
            {
                $this->currentMessage = $message;
                $this->currentContext = $context;
            }
        };
        $this->logger = new LoggerAdapter('TEST', 'default', $logger);

        //when
        $this->logger->info('Message', ['number' => 42, 'key' => 'value']);

        //then
        Assert::thatString($logger->currentMessage)->contains('TEST info: [ID: ] Message');
        Assert::thatArray($logger->currentContext)->isEqualTo(['number' => 42, 'key' => 'value']);
    }

    #[Test]
    public function shouldWriteErrorMessage()
    {
        //when
        $this->logger->error('My error log line with param 42 and Zaphod.');

        //then
        $logContent = $this->readStreamContent('test://stderr');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST error: [ID: ] My error log line with param 42 and Zaphod.');
    }

    #[Test]
    public function shouldWriteInfoMessage()
    {
        //when
        $this->logger->info('My info log line with param 42 and Zaphod.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST info: [ID: ] My info log line with param 42 and Zaphod.');
    }

    #[Test]
    public function shouldNotWriteInfoMessageIfMinimalLevelIsSetToWarning()
    {
        //given
        Config::overrideProperty('logger', 'default', 'minimal_levels')->with(['TEST' => LOG_WARNING]);
        $this->logger = new LoggerAdapter('TEST', 'default', new StdOutputLogger('TEST', 'default', 'test'));

        //when
        $this->logger->info('My info log line with param 42 and Zaphod.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->isEmpty();
    }

    #[Test]
    public function shouldWriteInfoMessageIfMinimalLevelIsSetToInfo()
    {
        //given
        Config::overrideProperty('logger', 'default', 'minimal_levels')->with(['TEST' => LOG_INFO]);
        $this->logger = new LoggerAdapter('TEST', 'default', new StdOutputLogger('TEST', 'default', 'test'));

        //when
        $this->logger->info('My info log line with param 42 and Zaphod.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->isNotEmpty();
    }

    #[Test]
    public function shouldWriteWarningMessage()
    {
        //when
        $this->logger->warning('My warning log line without params.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST warning: [ID: ] My warning log line without params.');
    }

    #[Test]
    public function shouldWriteCriticalMessage()
    {
        //when
        $this->logger->critical('My fatal log line without params.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST critical: [ID: ] My fatal log line without params.');
    }

    #[Test]
    public function shouldWriteDebugMessageIfDebugIsOn()
    {
        //given
        Config::overrideProperty('debug')->with(true);

        //when
        $this->logger->debug('My debug log line without params.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains('2014-01-01 11:11:11: TEST debug: [ID: ] My debug log line without params.');
    }

    #[Test]
    public function shouldIgnoreDebugMessageIfDebugIsOff()
    {
        //given
        Config::overrideProperty('debug')->with(false);

        //when
        $this->logger->debug('My debug log line without params.');

        //then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->hasSize(0);
        Config::clearProperty('debug');
    }

    #[Test]
    public function shouldGetLoggerInterface(): void
    {
        // given
        $loggerInterface = $this->logger->asLoggerInterface();

        // when
        // then
        Assert::that($loggerInterface)->isInstanceOf(LoggerInterface::class);
    }

    #[Test]
    #[DataProvider('logLevels')]
    public function shouldWriteLoggingIsDelegatedToLoggerInterfaceLogWhenLogIsCalled(string $logLevel): void
    {
        // given
        Config::overrideProperty('debug')->with(true);
        $loggerInterface = $this->logger->asLoggerInterface();

        // when
        $loggerInterface->log($logLevel,  'My log line');

        // then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains("2014-01-01 11:11:11: TEST $logLevel: [ID: ] My log line");
    }

    #[Test]
    #[DataProvider('logLevels')]
    public function shouldWriteLogWhenLoggingIsDelegatedToLoggerInterfaceAndAppropriateMethodIsCalled(string $logLevel): void
    {
        // given
        Config::overrideProperty('debug')->with(true);
        $loggerInterface = $this->logger->asLoggerInterface();

        // when
        $loggerInterface->$logLevel('My log line');

        // then
        $logContent = $this->readStreamContent('test://stdout');
        Assert::thatString($logContent)->contains("2014-01-01 11:11:11: TEST $logLevel: [ID: ] My log line");
    }

    public static function logLevels(): array
    {
        return [
            [LogLevel::INFO],
            [LogLevel::DEBUG],
            [LogLevel::ALERT],
            [LogLevel::CRITICAL],
            [LogLevel::ERROR],
            [LogLevel::WARNING],
            [LogLevel::NOTICE],
            [LogLevel::EMERGENCY],
        ];

    }


    private function readStreamContent(string $streamFile): string
    {
        return file_get_contents($streamFile);
    }
}
