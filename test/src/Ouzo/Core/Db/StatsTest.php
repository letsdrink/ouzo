<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Config;
use Ouzo\Db\Stats;
use Ouzo\FrontController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StatsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Stats::reset();
        Config::overrideProperty('debug')->with(true);

        FrontController::$requestId = null;
    }

    #[Test]
    public function shouldTraceQueryWithParams()
    {
        // when
        $result = Stats::trace('SELECT * FROM table WHERE id = ?', '10', function () {
            sleep(1);
            return 'result';
        });

        // then
        $this->assertEquals('result', $result);
        $this->assertCount(1, Stats::$queries);

        $this->assertGreaterThanOrEqual(Stats::getTotalTime(), Stats::$queries[0]['time']);
        $this->assertNotEquals('0.0000', Stats::$queries[0]['time']);
        $this->assertEquals('SELECT * FROM table WHERE id = ?', Stats::$queries[0]['query']);
        $this->assertEquals('10', Stats::$queries[0]['params']);
    }
}
