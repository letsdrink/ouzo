<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;

use Ouzo\Config;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SyslogLoggerTest extends TestCase
{
    private LoggerAdapter $logger;
    private SyslogAdapter|MockInterface $syslogAdapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->syslogAdapter = Mock::create(SyslogAdapter::class);
        $this->logger = new LoggerAdapter('TEST', 'default', new SyslogLogger('TEST', 'default', $this->syslogAdapter));
    }

    protected function tearDown(): void
    {
        Config::clearProperty('logger', 'default', 'minimal_levels');
        parent::tearDown();
    }

    #[Test]
    public function shouldWriteErrorMessage()
    {
        //when
        $this->logger->error('My error log line with param 42 and Zaphod.');

        //then
        Mock::verify($this->syslogAdapter)->log(LOG_ERR, 'TEST error: [ID: ] My error log line with param 42 and Zaphod.');
    }
}
