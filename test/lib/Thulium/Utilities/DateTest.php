<?php
use Thulium\Utilities\Clock;
use Thulium\Utilities\Date;

class DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldFormatDateDefaultFormatter()
    {
        //given
        $date = '2013-05-10 16:32:30.147177';

        //when
        $formattedDate = Date::formatDate($date);

        //then
        $this->assertEquals('2013-05-10', $formattedDate);
    }

    /**
     * @test
     */
    public function shouldFormatDateCustomFormatter()
    {
        //given
        $date = '2013-05-10 16:32:30.147177';

        //when
        $formattedDate = Date::formatDate($date, 'Y/m/d');

        //then
        $this->assertEquals('2013/05/10', $formattedDate);
    }

    /**
     * @test
     */
    public function shouldFormatDateTimeDefaultFormatter()
    {
        //given
        $date = '2013-05-10 16:32:30.147177';

        //when
        $formattedDateTime = Date::formatDateTime($date);

        //then
        $this->assertEquals('2013-05-10 16:32', $formattedDateTime);
    }

    /**
     * @test
     */
    public function shouldFormatDateTimeCustomFormatter()
    {
        //given
        $date = '2013-05-10 16:32:30.147177';

        //when
        $formattedDateTime = Date::formatDateTime($date, 'Y/m/d H-i-s');

        //then
        $this->assertEquals('2013/05/10 16-32-30', $formattedDateTime);
    }

    /**
     * @test
     */
    public function shouldAddIntervalToCurrentDate()
    {
        //when
        $date = Date::addInterval('P2Y');

        //then
        $this->assertGreaterThan(Clock::nowAsString(), $date);
    }

    /**
     * @test
     */
    public function shouldModifyCurrentDate()
    {
        //when
        $date = Date::modifyNow('2 days');

        //then
        $this->assertGreaterThan(Clock::nowAsString(), $date);
    }

    public function intervalsAndDates()
    {
        return array(
            array('1 day', '2010-01-21 12:00'),
            array('2 days', '2010-01-22 12:00'),
            array('1 hour', '2010-01-20 13:00'),
            array('2 hours', '2010-01-20 14:00'),
            array('25 hours', '2010-01-21 13:00'),
            // support for Postgres interval type (hours)
            array('01:00:00', '2010-01-20 13:00'),
            array('02:00:00', '2010-01-20 14:00'),
            array('25:00:00', '2010-01-21 13:00')
        );
    }

    /**
     * @test
     * @dataProvider intervalsAndDates
     */
    public function shouldModifyDate($interval, $expectedDate)
    {
        //when
        $date = Date::modify('2010-01-20 12:00', $interval);

        //then
        $this->assertEquals($expectedDate, $date);
    }
}