<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\TimeAgo;

class TimeAgoTest extends PHPUnit_Framework_TestCase
{
    public function dates()
    {
        return array(
            array('2012-02-20 12:00', '2012-02-20 11:59', 'timeAgo.justNow', array()),
            array('2012-02-20 12:00', '2012-02-20 11:55', 'timeAgo.minAgo', array('label' => 5)),
            array('2012-02-20 12:00', '2012-02-20 11:00', 'timeAgo.todayAt', array('label' => '11:00')),
            array('2012-02-20 12:00', '2012-02-19 12:00', 'timeAgo.yesterdayAt', array('label' => '12:00')),
            array('2012-02-20 12:00', '2012-01-20 12:00', 'timeAgo.thisYear', array('day' => '20', 'month' => 'timeAgo.month.1')),
            array('2012-02-20 12:00', '2012-01-19 12:00', 'timeAgo.thisYear', array('day' => '19', 'month' => 'timeAgo.month.1')),
            array('2012-02-20 12:00', '2012-01-20 11:59', 'timeAgo.thisYear', array('day' => '20', 'month' => 'timeAgo.month.1')),
            array('2012-02-20 12:00', '2012-01-20 11:55', 'timeAgo.thisYear', array('day' => '20', 'month' => 'timeAgo.month.1')),
            array('2012-02-20 12:00', '2011-01-20 12:00', '2011-01-20', array()),
            array('2012-02-20 12:00', '2011-01-19 12:00', '2011-01-19', array()),
            array('2012-02-20 12:00', '2011-01-20 11:59', '2011-01-20', array()),
            array('2012-02-20 12:00', '2011-01-20 11:55', '2011-01-20', array())
        );
    }

    /**
     * @test
     * @dataProvider dates
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
