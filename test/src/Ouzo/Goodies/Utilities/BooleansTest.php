<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Booleans;
use Ouzo\Utilities\Objects;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class BooleansTest extends TestCase
{
    #[Test]
    #[DataProvider('toBoolean')]
    public function shouldConvertToBoolean(mixed $string, bool $expected): void
    {
        //when
        $toBoolean = Booleans::toBoolean($string);

        //then
        $this->assertEquals($expected, $toBoolean, 'To convert: ' . Objects::toString($string) . ' Expected: ' . Objects::toString($expected));
    }

    public static function toBoolean(): array
    {
        return [
            [true, true],
            [null, false],
            ['true', true],
            ['TRUE', true],
            ['tRUe', true],
            ['on', true],
            ['yes', true],
            ['false', false],
            ['x gti', false],
            ['0', false],
            ['1', true],
            ['2', true],
            [0, false],
            [1, true],
            [2, true]
        ];
    }
}
