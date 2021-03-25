<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\TimeAgo;

use PHPUnit\Framework\TestCase;

class TimeAgoTest extends TestCase
{
    public function dates()
    {
        return [
            ['2012-02-20 12:00', '2012-02-20 11:59', 'timeAgo.justNow', []],
            ['2012-02-20 12:00', '2012-02-20 11:55', 'timeAgo.minAgo', ['label' => 5]],
            ['2012-02-20 12:00', '2012-02-20 11:00', 'timeAgo.todayAt', ['label' => '11:00']],
            ['2012-02-20 12:00', '2012-02-19 12:00', 'timeAgo.yesterdayAt', ['label' => '12:00']],
            ['2012-02-20 12:00', '2012-01-20 12:00', 'timeAgo.thisYear', ['day' => '20', 'month' => 'timeAgo.month.1']],
            ['2012-02-20 12:00', '2012-01-19 12:00', 'timeAgo.thisYear', ['day' => '19', 'month' => 'timeAgo.month.1']],
            ['2012-02-20 12:00', '2012-01-20 11:59', 'timeAgo.thisYear', ['day' => '20', 'month' => 'timeAgo.month.1']],
            ['2012-02-20 12:00', '2012-01-20 11:55', 'timeAgo.thisYear', ['day' => '20', 'month' => 'timeAgo.month.1']],
            ['2012-02-20 12:00', '2011-01-20 12:00', '2011-01-20', []],
            ['2012-02-20 12:00', '2011-01-19 12:00', '2011-01-19', []],
            ['2012-02-20 12:00', '2011-01-20 11:59', '2011-01-20', []],
            ['2012-02-20 12:00', '2011-01-20 11:55', '2011-01-20', []]
        ];
    }

    /**
     * @test
     * @dataProvider dates
     * @param string $currentDate
     * @param string $date
     * @param string $expectedKey
     * @param array $expectedParams
     */
    public function shouldCreateTimeAgo($currentDate, $date, $expectedKey, $expectedParams)
    {
        //given
        Clock::freeze($currentDate);

        //when
        $timeAgo = TimeAgo::create($date);

        //then
        $this->assertEquals($expectedKey, $timeAgo->getKey());
        $this->assertEquals($expectedParams, $timeAgo->getParams());
    }
}
