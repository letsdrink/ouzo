<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
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
    }

    /**
     * @test
     */
    public function shouldChangeIfDateIsBeforeAnotherDate()
    {
        $this->assertTrue(Clock::at('2011-01-01 12:34:13')->isBefore(Clock::at('2011-01-02 12:34:13')));
        $this->assertFalse(Clock::at('2011-01-02 12:34:13')->isBefore(Clock::at('2011-01-01 12:34:13')));
    }

    /**
     * @test
     */
    public function shouldCreateClockForGivenTimestamp()
    {
        //given
        $clock = Clock::fromTimestamp(1427207001);

        //when
        $result = $clock->format();

        //then
        $this->assertEquals('2015-03-24 15:23:21', $result);
    }
}
