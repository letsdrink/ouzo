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
        $this->assertGreaterThan(Clock::now(), $date);
    }
}