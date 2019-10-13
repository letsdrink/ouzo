<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
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
        $creator = new ProxyManagerInstanceCreator(new Configuration());

        //when
        $instance = $creator->create(ProxyManagerTestClass::class, null);

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
        $creator = new ProxyManagerInstanceCreator(new Configuration());
        $instance = $creator->create(ProxyManagerTestClass::class, null);

        //when
        unset($instance->field);

        //then
        $this->assertTrue(self::$constructorInvoked);
    }

    /**
     * @test
     */
    public function shouldLazyCreateInstanceViaInjector()
    {
        //given
        self::$constructorInvoked = false;
        $config = new InjectorConfig();
        $config->setLazyInstanceCreator(new ProxyManagerInstanceCreator(new Configuration()));
        $config->bind(ProxyManagerTestClass::class)->in(Scope::SINGLETON);
        $injector = new Injector($config);
        $instance1 = $injector->getInstance(ProxyManagerTestClass::class);

        //when
        $instance2 = $injector->getInstance(ProxyManagerTestClass::class);

        //then
        $this->assertInstanceOf(ProxyManagerTestClass::class, $instance1);
        $this->assertInstanceOf(ProxyManagerTestClass::class, $instance2);
        $this->assertSame($instance1, $instance2);
        $this->assertFalse(self::$constructorInvoked);
    }
}
