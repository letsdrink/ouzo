<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\TranslatableTimeAgo;
use Ouzo\Utilities\Clock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TranslatableTimeAgoTest extends TestCase
{
    #[DataProvider('dates')]
    public function shouldCreateTranslatableTimeAgo($currentDate, $date, $expectedText): void
    {
        //given
        Clock::freeze($currentDate);

        //when
        $translatableTimeAgo = TranslatableTimeAgo::create($date)->asString();

        //then
        $this->assertEquals($expectedText, $translatableTimeAgo, 'Error in [' . $date . '] with expected [' . $expectedText . ']');
    }

    public static function dates(): array
    {
        return [
            ['2012-02-20 12:00', '2012-02-20 11:59', 'just now'],
            ['2012-02-20 12:00', '2012-02-20 11:55', '5 min. ago'],
            ['2012-02-20 12:00', '2012-02-20 11:00', 'today at 11:00'],
            ['2012-02-20 12:00', '2012-02-19 12:00', 'yesterday at 12:00'],
            ['2012-02-20 12:00', '2012-01-20 12:00', 'Jan 20'],
            ['2012-02-20 12:00', '2012-01-19 12:00', 'Jan 19'],
            ['2012-02-20 12:00', '2012-01-20 11:59', 'Jan 20'],
            ['2012-02-20 12:00', '2012-01-20 11:55', 'Jan 20'],
            ['2012-02-20 12:00', '2011-01-20 12:00', '2011-01-20'],
            ['2012-02-20 12:00', '2011-01-19 12:00', '2011-01-19'],
            ['2012-02-20 12:00', '2011-01-20 11:59', '2011-01-20'],
            ['2012-02-20 12:00', '2011-01-20 11:55', '2011-01-20']
        ];
    }
}
