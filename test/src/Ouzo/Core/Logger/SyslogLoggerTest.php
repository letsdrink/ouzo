<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Logger;


use Ouzo\Config;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\SimpleMock;
use Ouzo\Utilities\Clock;
use Psr\Log\LoggerInterface;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SyslogLoggerTest extends TestCase
{
    private LoggerInterface $logger;
    private SyslogAdapter $syslogAdapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->syslogAdapter = Mock::create(SyslogAdapter::class);
        $this->logger = new SyslogLogger('TEST', 'default', $this->syslogAdapter);
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
        $this->logger->error('My error log line with param %s and %s.', [42, 'Zaphod']);

        //then
        Mock::verify($this->syslogAdapter)->log(LOG_ERR, 'TEST error: [ID: ] My error log line with param 42 and Zaphod.');
    }

}
