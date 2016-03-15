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
    public function shouldInjectSubClassDependency()
    {
        // given
        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep')->to('\SubClassWithNoDep');
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance('\ClassWithDep');

        //then
        $this->assertInstanceOf('\ClassWithDep', $instance);
        $this->assertDependencyInjected('\SubClassWithNoDep', $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldCreateSubClass()
    {
        // given
        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep')->to('\SubClassWithNoDep');
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance('\ClassWithNoDep');

        //then
        $this->assertInstanceOf('\SubClassWithNoDep', $instance);
    }

    /**
     * @test
     */
    public function shouldReturnBoundInstance()
    {
        // given
        $object = new ClassWithNoDep();

        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep')->toInstance($object);
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance('\ClassWithNoDep');

        //then
        $this->assertSame($object, $instance);
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

    /**
     * @test
     */
    public function shouldInjectNamedDependencyWhenNameWasNotBound()
    {
        //when
        $instance = $this->injector->getInstance('\ClassWithNamedDep');

        //then
        $this->assertInstanceOf('\ClassWithNamedDep', $instance);
        $this->assertDependencyInjected('\ClassWithNoDep', $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectNamedDependency()
    {
        // given
        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep', 'my_dep');
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance('\ClassWithNamedDep');

        //then
        $this->assertInstanceOf('\ClassWithNamedDep', $instance);
        $this->assertDependencyInjected('\ClassWithNoDep', $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectProperNamedDependency()
    {
        // given
        $config = new InjectorConfig();
        $config->bind('\ClassWithNoDep', 'my_dep')->to('\SubClassWithNoDep');
        $config->bind('\ClassWithNoDep', 'other_dep');
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance('\ClassWithNamedDep');

        //then
        $this->assertInstanceOf('\ClassWithNamedDep', $instance);
        $this->assertDependencyInjected('\SubClassWithNoDep', $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldReturnInstanceByNameEvenWhenNameWasNotBound()
    {
        //when
        $instance = $this->injector->getInstance('\ClassWithNoDep', 'some_name');

        //then
        $this->assertInstanceOf('\ClassWithNoDep', $instance);
    }

    /**
     * @test
     */
    public function shouldInjectItself()
    {
        // given
        $config = new InjectorConfig();
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance('\Ouzo\Injection\Injector');

        //then
        $this->assertSame($injector, $instance);
    }

    private function assertDependencyInjected($className, $instance)
    {
        $this->assertNotNull($instance, 'Dependency was not injected.');
        $this->assertInstanceOf($className, $instance);
    }
}
