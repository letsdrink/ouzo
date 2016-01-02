<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\Scope;
use Ouzo\Tests\CatchException;


class InjectorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Injector
     */
    private $injector;

    public function setUp()
    {
        parent::setUp();
        $this->injector = new Injector();
    }

    /**
     * @test
     */
    public function shouldCreateInstanceByName()
    {
        //when
        $instance = $this->injector->getInstance('\ClassWithNoDep');

        //then
        $this->assertInstanceOf('\ClassWithNoDep', $instance);
    }

    /**
     * @test
     */
    public function shouldCreatePrototype()
    {
        //given
        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep')->in(Scope::PROTOTYPE);
        $injector = new Injector($config);
        $instance1 = $injector->getInstance('\ClassWithNoDep');

        //when
        $instance2 = $injector->getInstance('\ClassWithNoDep');

        //then
        $this->assertInstanceOf('\ClassWithNoDep', $instance1);
        $this->assertInstanceOf('\ClassWithNoDep', $instance2);
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * @test
     */
    public function shouldCreateSingleton()
    {
        //given
        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep')->in(Scope::SINGLETON);
        $injector = new Injector($config);
        $instance1 = $injector->getInstance('\ClassWithNoDep');

        //when
        $instance2 = $injector->getInstance('\ClassWithNoDep');

        //then
        $this->assertInstanceOf('\ClassWithNoDep', $instance1);
        $this->assertInstanceOf('\ClassWithNoDep', $instance2);
        $this->assertSame($instance1, $instance2);
    }

    /**
     * @test
     */
    public function shouldInjectDependency()
    {
        //when
        $instance = $this->injector->getInstance('\ClassWithDep');

        //then
        $this->assertInstanceOf('\ClassWithDep', $instance);
        $this->assertDependencyInjected('\ClassWithNoDep', $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectDeepDependency()
    {
        //when
        $instance = $this->injector->getInstance('\ClassWithDeepDep');

        //then
        $this->assertInstanceOf('\ClassWithDeepDep', $instance);
        $this->assertDependencyInjected('\ClassWithDep', $instance->classWithDep);
        $this->assertDependencyInjected('\ClassWithNoDep', $instance->classWithDep->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectPrivateDependency()
    {
        //when
        $instance = $this->injector->getInstance('\ClassWithPrivateDep');

        //then
        $this->assertInstanceOf('\ClassWithPrivateDep', $instance);
        $this->assertDependencyInjected('\ClassWithNoDep', $instance->getMyClass());
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenVarNotDefinedForInject()
    {
        //when
        CatchException::when($this->injector)->getInstance('\ClassWithInvalidDep');

        //then
        CatchException::assertThat()->isInstanceOf('\Ouzo\Injection\InjectorException');
    }

    private function assertDependencyInjected($className, $instance)
    {
        $this->assertNotNull($instance, 'Dependency was not injected.');
        $this->assertInstanceOf($className, $instance);
    }
}
