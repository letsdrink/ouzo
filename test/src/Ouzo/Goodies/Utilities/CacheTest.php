<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Utilities\Cache;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CacheTest extends TestCase
{
    public static int $call_count = 0;

    public function setUp(): void
    {
        parent::setUp();
        Cache::clear();
        CacheTest::$call_count = 0;
    }

    #[Test]
    public function shouldCacheGets()
    {
        //given
        $function = fn() => ++CacheTest::$call_count;

        //when
        $result1 = Cache::get("id", $function);
        $result2 = Cache::get("id", $function);

        //then
        $this->assertEquals(1, CacheTest::$call_count);
        $this->assertEquals(1, $result1);
        $this->assertEquals(1, $result2);
    }

    public function methodWithCachedClosure()
    {
        return Cache::memoize(fn() => ++CacheTest::$call_count);
    }

    #[Test]
    public function shouldCacheClosure()
    {
        //when
        $result1 = $this->methodWithCachedClosure();
        $result2 = $this->methodWithCachedClosure();

        //then
        $this->assertEquals(1, CacheTest::$call_count);
        $this->assertEquals(1, $result1);
        $this->assertEquals(1, $result2);
    }

    #[Test]
    public function shouldUseDifferentKeysForDifferentClosures()
    {
        //when
        $result1 = Cache::memoize(fn() => 1);
        $result2 = Cache::memoize(fn() => 2);

        //then
        $this->assertEquals(2, Cache::size());
        $this->assertEquals(1, $result1);
        $this->assertEquals(2, $result2);
    }

    #[Test]
    public function shouldCacheNullValues()
    {
        //given
        $function = function () {
            ++CacheTest::$call_count;
            return null;
        };

        //when
        $result1 = Cache::get("id", $function);
        $result2 = Cache::get("id", $function);

        //then
        $this->assertEquals(1, CacheTest::$call_count);
        $this->assertNull($result1);
        $this->assertNull($result2);
    }
}
