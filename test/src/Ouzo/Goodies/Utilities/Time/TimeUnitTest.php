<?php

namespace Ouzo\Utilities\Time;

use PHPUnit\Framework\TestCase;

class TimeUnitTest extends TestCase
{
    /**
     * @test
     * @dataProvider times
     */
    public function shouldConvertNanos(int $time, string $timeUnit, int $expectedTime)
    {
        //when
        $result = TimeUnit::convert($time, $timeUnit);

        //then
        $this->assertEquals($expectedTime, $result);
    }

    public function times(): array
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
}
