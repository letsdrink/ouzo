<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */
use Ouzo\Utilities\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    public static $call_count = 0;

    public function setUp()
    {
        parent::setUp();
        Cache::clear();
        CacheTest::$call_count = 0;
    }

    /**
     * @test
     */
    public function shouldCacheGets()
    {
        //given
        $function = function () {
            return ++CacheTest::$call_count;
        };

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
        return Cache::memoize(function () {
            return ++CacheTest::$call_count;
        });
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function shouldUseDifferentKeysForDifferentClosures()
    {
        //when
        $result1 = Cache::memoize(function () {
            return 1;
        });
        $result2 = Cache::memoize(function () {
            return 2;
        });

        //then
        $this->assertEquals(2, Cache::size());
        $this->assertEquals(1, $result1);
        $this->assertEquals(2, $result2);
    }

    /**
     * @test
     */
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
