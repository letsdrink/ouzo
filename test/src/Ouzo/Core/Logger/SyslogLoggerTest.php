<?php

namespace Ouzo\Logger;


use Ouzo\Config;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Clock;
use Psr\Log\LoggerInterface;

use PHPUnit\Framework\TestCase;

class SyslogLoggerTest extends TestCase
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SyslogAdapter
     */
    private $syslogLogProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->syslogLogProvider = Mock::create(SyslogAdapter::class);
        $this->logger = new SyslogLogger('TEST', 'default', $this->syslogLogProvider);
    }

    protected function tearDown(): void
    {
        Config::clearProperty('logger', 'default', 'minimal_levels');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldWriteErrorMessage()
    {
        //when
        $this->logger->error('My error log line with param %s and %s.', [42, 'Zaphod']);

        //then
        Mock::verify($this->syslogLogProvider)->log(LOG_ERR, 'TEST error: [ID: ] My error log line with param 42 and Zaphod.');
    }

}
