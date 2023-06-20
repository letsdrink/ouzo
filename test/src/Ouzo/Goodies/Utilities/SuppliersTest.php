<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Tests\Mock\Mock;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Suppliers;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SuppliersTest extends TestCase
{
    #[Test]
    public function memoizeWithExpirationShouldCacheResultForGivenTime()
    {
        //given
        $command = Mock::mock();
        Mock::when($command)->getName()->thenReturn('Jack', 'Black', 'White');

        $supplier = Suppliers::memoizeWithExpiration(fn() => $command->getName(), 10);

        //when
        Clock::freeze('2014-01-01 11:11:11');
        $result1 = $supplier->get();
        $result2 = $supplier->get();
        Clock::freeze('2014-01-01 11:11:12');
        $result3 = $supplier->get();
        Clock::freeze('2014-01-01 11:11:22');
        $result4 = $supplier->get();
        $result5 = $supplier->get();

        //then
        $this->assertEquals('Jack', $result1);
        $this->assertEquals('Jack', $result2);
        $this->assertEquals('Jack', $result3);
        $this->assertEquals('Black', $result4);
        $this->assertEquals('Black', $result5);
    }

    #[Test]
    public function memoizeShouldCacheResult()
    {
        //given
        $command = Mock::mock();
        Mock::when($command)->getName()->thenReturn('Jack', 'Black', 'White');

        $supplier = Suppliers::memoize(fn() => $command->getName());

        //when
        Clock::freeze('2014-01-01 11:11:11');
        $result1 = $supplier->get();
        $result2 = $supplier->get();
        Clock::freeze('2014-01-01 11:11:12');
        $result3 = $supplier->get();
        Clock::freeze('2014-01-01 11:11:22');
        $result4 = $supplier->get();
        $result5 = $supplier->get();

        //then
        $this->assertEquals('Jack', $result1);
        $this->assertEquals('Jack', $result2);
        $this->assertEquals('Jack', $result3);
        $this->assertEquals('Jack', $result4);
        $this->assertEquals('Jack', $result5);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Clock::freeze();
    }
}
