<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Tests\Assert;
use Ouzo\Utilities\Clock;

class ClockTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldResetFreezeDate()
    {
        //given
        Clock::$freezeDate = new DateTime('2013-12-11 15:52:01');

        //when
        Clock::freeze();

        //then
        $this->assertNotEquals('2013-12-11 15:52:01', Clock::nowAsString());
    }

    /**
     * @test
     */
    public function shouldFreezeGivenDate()
    {
        //when
        Clock::freeze('2011-01-02 12:34');

        //then
        $this->assertNotEquals('2011-01-02 12:34', Clock::nowAsString());
    }

    /**
     * @test
     */
    public function shouldUseGivenFormat()
    {
        //given
        Clock::freeze('2011-01-02 12:34');

        //when
        $result = Clock::nowAsString('Y-m-d');

        //then
        $this->assertEquals('2011-01-02', $result);
    }

    /**
     * @test
     */
    public function shouldAddIntervals()
    {
        //given
        Clock::freeze('2000-01-01 00:00:00');

        //when
        $result = Clock::now()
            ->plusYears(1)
            ->plusMonths(2)
            ->plusDays(3)
            ->plusHours(4)
            ->plusMinutes(5)
            ->plusSeconds(6)
            ->format();

        //then
        $this->assertEquals('2001-03-04 04:05:06', $result);
    }

    /**
     * @test
     */
    public function shouldSubtractIntervals()
    {
        //given
        Clock::freeze('2001-03-04 04:05:06');

        //when
        $result = Clock::now()
            ->minusYears(1)
            ->minusMonths(2)
            ->minusDays(3)
            ->minusHours(4)
            ->minusMinutes(5)
            ->minusSeconds(6)
            ->format();

        //then
        $this->assertEquals('2000-01-01 00:00:00', $result);
    }

    /**
     * @test
     */
    public function shouldReturnTimestamp()
    {
        //given
        Clock::freeze('2011-01-02 12:34');

        //when
        $timestamp = Clock::now()->getTimestamp();

        //then
        $dateTime = new DateTime('2011-01-02 12:34');
        $this->assertEquals($dateTime->getTimestamp(), $timestamp);
    }

    /**
     * @test
     */
    public function shouldCreateClockForGivenDate()
    {
        //given
        $clock = Clock::at('2011-01-02 12:34:13');

        //when
        $result = $clock->format();

        //then
        $this->assertEquals('2011-01-02 12:34:13', $result);
    }

    /**
     * @test
     */
    public function shouldChangeIfDateIsAfterAnotherDate()
    {
        $this->assertTrue(Clock::at('2011-01-02 12:34:13')->isAfter(Clock::at('2011-01-01 12:34:13')));
        $this->assertFalse(Clock::at('2011-01-01 12:34:13')->isAfter(Clock::at('2011-01-02 12:34:13')));

        $this->assertTrue(Clock::at('2011-01-02 12:34:13')->isAfterOrEqualTo(Clock::at('2011-01-01 12:34:13')));
        $this->assertFalse(Clock::at('2011-01-01 12:34:13')->isAfterOrEqualTo(Clock::at('2011-01-02 12:34:13')));
        $this->assertTrue(Clock::at('2011-01-02 12:34:13')->isAfterOrEqualTo(Clock::at('2011-01-02 12:34:13')));
    }

    /**
     * @test
     */
    public function shouldChangeIfDateIsBeforeAnotherDate()
    {
        $this->assertTrue(Clock::at('2011-01-01 12:34:13')->isBefore(Clock::at('2011-01-02 12:34:13')));
        $this->assertFalse(Clock::at('2011-01-02 12:34:13')->isBefore(Clock::at('2011-01-01 12:34:13')));

        $this->assertTrue(Clock::at('2011-01-01 12:34:13')->isBeforeOrEqualTo(Clock::at('2011-01-02 12:34:13')));
        $this->assertFalse(Clock::at('2011-01-02 12:34:13')->isBeforeOrEqualTo(Clock::at('2011-01-01 12:34:13')));
        $this->assertTrue(Clock::at('2011-01-01 12:34:13')->isBeforeOrEqualTo(Clock::at('2011-01-01 12:34:13')));
    }

    /**
     * @test
     */
    public function shouldCreateClockForGivenTimestamp()
    {
        //given
        $clock = Clock::fromTimestamp(1427207001)->withTimezone('UTC');

        //when
        $result = $clock->format();

        //then
        $this->assertEquals('2015-03-24 14:23:21', $result);
    }

    /**
     * @test
     */
    public function shouldNotModifyClock()
    {
        // given
        $clock = new Clock(new DateTime('2017-02-06 14:00:00'));

        // when
        $modifiedClock = $clock->plusHours(2);

        // then
        Assert::thatString($clock->format())->isEqualTo('2017-02-06 14:00:00');
        Assert::thatString($modifiedClock->format())->isEqualTo('2017-02-06 16:00:00');
    }

    /**
     * @test
     */
    public function shouldNotModifyTimeZone()
    {
        // given
        $dateTime = new DateTime('2017-02-06 14:00:00', new DateTimeZone('Europe/Warsaw'));
        $clock = new Clock($dateTime);

        // when
        $modifiedClock = $clock->withTimezone('UTC');

        // then
        Assert::that($dateTime->getTimezone())->isEqualTo(new DateTimeZone('Europe/Warsaw'));
        Assert::that($modifiedClock->toDateTime()->getTimezone())->isEqualTo(new DateTimeZone('UTC'));
    }

    /**
     * @test
     */
    public function shouldHandleDSTChange()
    {
        //given
        $dateTime = new DateTime('2017-03-26 01:31:50', new DateTimeZone('Europe/Warsaw'));

        //when
        $clock = (new Clock($dateTime))->plusHours(1);

        //then
        $this->assertEquals('2017-03-26 03:31:50', $clock->format());
    }

    /**
     * @test
     */
    public function shouldHandleDSTChangeWhenAddingMultipleHours()
    {
        //given
        $dateTime = new DateTime('2017-03-26 01:31:50', new DateTimeZone('Europe/Warsaw'));

        //when
        $clock = (new Clock($dateTime))->plusHours(2);

        //then
        $this->assertEquals('2017-03-26 04:31:50', $clock->format());
    }

    function monthChange()
    {
        return [
            ['2017-01-01', 1, '2017-02-01'],
            ['2017-01-01', 2, '2017-03-01'],
            ['2017-01-27', 1, '2017-02-27'],
            ['2017-01-28', 1, '2017-02-28'],
            ['2017-01-29', 1, '2017-02-28'],
            ['2017-01-30', 1, '2017-02-28'],
            ['2017-01-31', 1, '2017-02-28'],
            ['2017-12-31', 1, '2018-01-31'],
            ['2017-02-01', -1, '2017-01-01'],
            ['2017-03-01', -2, '2017-01-01'],
            ['2017-02-28', -1, '2017-01-28'],
            ['2018-01-31', -1, '2017-12-31']
        ];
    }

    /**
     * @dataProvider monthChange
     * @test
     */
    public function shouldChangeMonthsProperly($date, $change, $expectedDate)
    {
        $this->assertEquals($expectedDate, Clock::at($date)->plusMonths($change)->format('Y-m-d'));
    }
}
