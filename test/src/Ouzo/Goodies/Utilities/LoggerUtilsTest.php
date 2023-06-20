<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LoggerUtilsTest extends TestCase
{
    #[Test]
    #[DataProvider('classes')]
    public function shouldShortenName($class, $length, $expected): void
    {
        //when
        $shortenClassName = LoggerUtils::shortenClassName($class, $length);

        //then
        $this->assertEquals($expected, $shortenClassName);
    }

    public static function classes(): array
    {
        return [
            ['Ouzo\Utilities\LoggerUtils', null, 'Ouzo\Utilities\LoggerUtils'],
            ['Ouzo\Utilities\LoggerUtils', 0, 'LoggerUtils'],
            ['Ouzo\Utilities\LoggerUtils', 10, 'O\U\LoggerUtils'],
            ['Really\Long\Name\Space\To\The\Class', 1, 'R\L\N\S\T\T\Class'],
            ['Really\Long\Name\Space\To\The\Class', 10, 'R\L\N\S\T\T\Class'],
            ['Really\Long\Name\Space\To\The\Class', 20, 'R\L\N\S\To\The\Class'],
            ['The\Class', 9, 'The\Class'],
            ['Class', 1, 'Class'],
            ['Class', 10, 'Class'],
            ['My Wrong Class', 10, 'My Wrong Class'],
        ];
    }
}
