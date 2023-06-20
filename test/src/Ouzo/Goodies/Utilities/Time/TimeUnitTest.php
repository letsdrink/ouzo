<?php

namespace Ouzo\Utilities\Time;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TimeUnitTest extends TestCase
{
    #[DataProvider('durations')]
    public function shouldConvertFromNanos(int $duration, string $timeUnit, int $expectedTime): void
    {
        //when
        $result = TimeUnit::of($timeUnit)->convert($duration, TimeUnit::NANOSECONDS);

        //then
        $this->assertEquals($expectedTime, $result);
    }

    public static function durations(): array
    {
        return [
            [10, TimeUnit::NANOSECONDS, 10],
            [11 * 1000, TimeUnit::MICROSECONDS, 11],
            [12 * 1000 * 1000, TimeUnit::MILLISECONDS, 12],
            [13 * 1000 * 1000 * 1000, TimeUnit::SECONDS, 13],
            [14 * 1000 * 1000 * 1000 * 60, TimeUnit::MINUTES, 14],
            [15 * 1000 * 1000 * 1000 * 60 * 60, TimeUnit::HOURS, 15],
            [16 * 1000 * 1000 * 1000 * 60 * 60 * 24, TimeUnit::DAYS, 16],
        ];
    }

    #[Test]
    public function shouldCreateTimeUnitByInstanceOfTimeUnit()
    {
        //when
        $result = TimeUnit::of(TimeUnit::days());

        //then
        $this->assertEquals(TimeUnit::DAYS, $result);
    }

    #[Test]
    public function shouldConvertToMillis()
    {
        //when
        $result = TimeUnit::millis()->convert(5 * 1000 * 1000, TimeUnit::NANOSECONDS);

        //then
        $this->assertEquals(5, $result);
    }

    #[Test]
    public function shouldConvertToMicros()
    {
        //when
        $result = TimeUnit::micros()->convert(5, TimeUnit::MILLISECONDS);

        //then
        $this->assertEquals(5 * 1000, $result);
    }

    #[Test]
    public function shouldConvertToSeconds()
    {
        //when
        $result = TimeUnit::seconds()->convert(5, TimeUnit::MINUTES);

        //then
        $this->assertEquals(5 * 60, $result);
    }

    #[Test]
    public function shouldConvertToMinutes()
    {
        //when
        $result = TimeUnit::minutes()->convert(5, TimeUnit::HOURS);

        //then
        $this->assertEquals(5 * 60, $result);
    }

    #[Test]
    public function shouldConvertToHours()
    {
        //when
        $result = TimeUnit::hours()->convert(5 * 60, TimeUnit::MINUTES);

        //then
        $this->assertEquals(5, $result);
    }

    #[Test]
    public function shouldConvertToDays()
    {
        //when
        $result = TimeUnit::days()->convert(2 * 24, TimeUnit::HOURS);

        //then
        $this->assertEquals(2, $result);
    }

    #[Test]
    public function shouldConvertToNanosByToMethod()
    {
        //when
        $result = TimeUnit::millis()->toNanos(3);

        //then
        $this->assertEquals(3 * 1000 * 1000, $result);
    }

    #[Test]
    public function shouldConvertToMillisByToMethod()
    {
        //when
        $result = TimeUnit::nanos()->toMillis(5 * 1000 * 1000);

        //then
        $this->assertEquals(5, $result);
    }

    #[Test]
    public function shouldConvertToMicrosByToMethod()
    {
        //when
        $result = TimeUnit::millis()->toMicros(5);

        //then
        $this->assertEquals(5 * 1000, $result);
    }

    #[Test]
    public function shouldConvertToSecondsByToMethod()
    {
        //when
        $result = TimeUnit::minutes()->toSeconds(5);

        //then
        $this->assertEquals(5 * 60, $result);
    }

    #[Test]
    public function shouldConvertToMinutesByToMethod()
    {
        //when
        $result = TimeUnit::hours()->toMinutes(5);

        //then
        $this->assertEquals(5 * 60, $result);
    }

    #[Test]
    public function shouldConvertToHoursByToMethod()
    {
        //when
        $result = TimeUnit::minutes()->toHours(5 * 60);

        //then
        $this->assertEquals(5, $result);
    }

    #[Test]
    public function shouldConvertToDaysByToMethod()
    {
        //when
        $result = TimeUnit::hours()->toDays(2 * 24);

        //then
        $this->assertEquals(2, $result);
    }
}
