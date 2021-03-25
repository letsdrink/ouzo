<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Injection\Creator;

use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\Scope;
use PHPUnit\Framework\TestCase;
use ProxyManager\Configuration;

class ProxyManagerInstanceCreatorTest extends TestCase
{
    /** @var bool */
    public static $constructorInvoked;

    /**
     * @test
     */
    public function shouldLazyCreateInstance()
    {
        // given
        self::$constructorInvoked = false;
        $injector = $this->createInjector();

        //when
        $instance = $injector->getInstance(ProxyManagerTestClass::class);

        //then
        $this->assertInstanceOf(ProxyManagerTestClass::class, $instance);
        $this->assertFalse(self::$constructorInvoked);
    }

    /**
     * @test
     */
    public function shouldInvokeConstructorAfterInteraction()
    {
        // given
        self::$constructorInvoked = false;
        $injector = $this->createInjector();
        $instance = $injector->getInstance(ProxyManagerTestClass::class);

        //when
        unset($instance->field);

        //then
        $this->assertTrue(self::$constructorInvoked);
    }

    /**
     * @test
     */
    public function shouldLazyCreateMultipleInstances()
    {
        //given
        self::$constructorInvoked = false;
        $injector = $this->createInjector();
        $instance1 = $injector->getInstance(ProxyManagerTestClass::class);

        //when
        $instance2 = $injector->getInstance(ProxyManagerTestClass::class);

        //then
        $this->assertInstanceOf(ProxyManagerTestClass::class, $instance1);
        $this->assertInstanceOf(ProxyManagerTestClass::class, $instance2);
        $this->assertSame($instance1, $instance2);
        $this->assertFalse(self::$constructorInvoked);
    }

    private function createInjector(): Injector
    {
        $config = new InjectorConfig();
        $config->setLazyInstanceCreator(new ProxyManagerInstanceCreator(new Configuration()));
        $config->bind(ProxyManagerTestClass::class)->in(Scope::SINGLETON);
        return new Injector($config);
    }
}
