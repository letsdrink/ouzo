<?php

namespace Ouzo;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

function getenv(?string $name, bool $local_only = false): array|string|false
{
    return false;
}

class EnvironmentTest extends TestCase
{
    #[Test]
    public function shouldInitializeWhenIsNotConsoleAndServerEnvironmentNotExist(): void
    {
        //given
        $environment = new Environment();

        //when
        CatchException::when($environment)->init();

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Can\'t determine configuration environment.');
    }
}
