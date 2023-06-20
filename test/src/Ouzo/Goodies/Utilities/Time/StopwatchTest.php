<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities\Time;

use LogicException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class StopwatchTest extends TestCase
{
    private Ticker|MockInterface $ticker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticker = Mock::create(Ticker::class);
    }

    #[Test]
    public function shouldMeasureElapsedTime()
    {
        //given
        Mock::when($this->ticker)->read()->thenReturn(0, 1, 2, 6);

        $stopwatch = Stopwatch::createStarted($this->ticker);

        //when
        $elapsed1 = $stopwatch->elapsed(TimeUnit::NANOSECONDS);
        $elapsed2 = $stopwatch->elapsed(TimeUnit::NANOSECONDS);
        $elapsed3 = $stopwatch->elapsed(TimeUnit::NANOSECONDS);

        //then
        $this->assertEquals(1, $elapsed1);
        $this->assertEquals(2, $elapsed2);
        $this->assertEquals(6, $elapsed3);
    }

    #[Test]
    public function shouldMeasureElapsedTimeAfterStop()
    {
        //given
        Mock::when($this->ticker)->read()->thenReturn(0, 10);

        $stopwatch = Stopwatch::createStarted($this->ticker);
        $stopwatch->stop();

        //when
        $elapsed = $stopwatch->elapsed(TimeUnit::NANOSECONDS);

        //then
        $this->assertEquals(10, $elapsed);
    }

    #[Test]
    public function shouldStartAfterStop()
    {
        //given
        $stopwatch = Stopwatch::createStarted($this->ticker);
        $stopwatch->stop();

        //when
        $stopwatch->start();

        //then
        $this->assertTrue($stopwatch->isRunning());
    }

    #[Test]
    public function shouldNotBeRunningAfterStop()
    {
        //given
        $stopwatch = Stopwatch::createStarted($this->ticker);

        //when
        $stopwatch->stop();

        //then
        $this->assertFalse($stopwatch->isRunning());
    }

    #[Test]
    public function shouldNotBeRunningAfterCreatingUnstarted()
    {
        //when
        $stopwatch = Stopwatch::createUnstarted($this->ticker);

        //then
        $this->assertFalse($stopwatch->isRunning());
    }

    #[Test]
    public function shouldBeRunningAfterCreatingStarted()
    {
        //when
        $stopwatch = Stopwatch::createStarted($this->ticker);

        //then
        $this->assertTrue($stopwatch->isRunning());
    }

    #[Test]
    public function shouldFailWhenStoppingAlreadyStopped()
    {
        //given
        $stopwatch = Stopwatch::createUnstarted($this->ticker);

        //when
        CatchException::when($stopwatch)->stop();

        //then
        CatchException::assertThat()->isInstanceOf(LogicException::class);
    }

    #[Test]
    public function shouldFailWhenStartingAlreadyStarted()
    {
        //given
        $stopwatch = Stopwatch::createStarted($this->ticker);

        //when
        CatchException::when($stopwatch)->start();

        //then
        CatchException::assertThat()->isInstanceOf(LogicException::class);
    }

    #[Test]
    public function shouldNotBeRunningAfterReset()
    {
        //given
        $stopwatch = Stopwatch::createStarted($this->ticker);

        //when
        $stopwatch->reset();

        //then
        $this->assertFalse($stopwatch->isRunning());
        $this->assertEquals(0, $stopwatch->elapsed(TimeUnit::NANOSECONDS));
    }

    #[DataProvider('elapsedTimes')]
    public function shouldReturnElapsedInTimeUnit(int $time, string $timeUnit, int $expectedTime): void
    {
        //given
        Mock::when($this->ticker)->read()->thenReturn(0, $time);

        $stopwatch = Stopwatch::createStarted($this->ticker);

        //when
        $elapsed = $stopwatch->elapsed($timeUnit);

        //then
        $this->assertEquals($expectedTime, $elapsed);
    }

    public static function elapsedTimes(): array
    {
        return [
            [10, TimeUnit::NANOSECONDS, 10],
            [11 * 1000 * 1000, TimeUnit::MILLISECONDS, 11],
            [12 * 1000 * 1000 * 1000 * 60 * 60 * 24, TimeUnit::DAYS, 12],
        ];
    }
}
