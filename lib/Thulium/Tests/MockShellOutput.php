<?php
namespace Thulium\Tests;

class MockShellOutput
{
    const NL = PHP_EOL;

    public function write($message, $newLines = 1)
    {
        echo $message . str_repeat(self::NL, $newLines);
    }
}