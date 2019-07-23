<?php
/*
 * Copyright (c) Ouzo contributors, http://ouzoframework.org
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\InjectorException;
use Ouzo\Injection\Scope;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\TestCase;


class InjectorTest extends TestCase
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
        $instance = $this->injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance);
    }

    /**
     * @test
     */
    public function shouldCreatePrototype()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->in(Scope::PROTOTYPE);
        $injector = new Injector($config);
        $instance1 = $injector->getInstance(ClassWithNoDep::class);

        //when
        $instance2 = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance1);
        $this->assertInstanceOf(ClassWithNoDep::class, $instance2);
        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * @test
     */
    public function shouldCreateSingleton()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->in(Scope::SINGLETON);
        $injector = new Injector($config);
        $instance1 = $injector->getInstance(ClassWithNoDep::class);

        //when
        $instance2 = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance1);
        $this->assertInstanceOf(ClassWithNoDep::class, $instance2);
        $this->assertSame($instance1, $instance2);
    }

    /**
     * @test
     */
    public function shouldInjectDependency()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithDep::class);

        //then
        $this->assertInstanceOf(ClassWithDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectDeepDependency()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithDeepDep::class);

        //then
        $this->assertInstanceOf(ClassWithDeepDep::class, $instance);
        $this->assertDependencyInjected(ClassWithDep::class, $instance->classWithDep);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->classWithDep->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectDependencyByConstructorBoundToOtherClass()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->to(SubClassWithNoDep::class);
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithConstructorDep::class);

        //then
        $this->assertInstanceOf(ClassWithConstructorDep::class, $instance);
        $this->assertDependencyInjected(SubClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectNamedDependencyByConstructorBoundToOtherClass()
    {
        // given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class, 'my_dep')->to(SubClassWithNoDep::class);
        $config->bind(ClassWithNoDep::class, 'other_dep')->to(ClassWithNoDep::class);
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithNamedConstructorDep::class);

        //then
        $this->assertInstanceOf(ClassWithNamedConstructorDep::class, $instance);
        $this->assertDependencyInjected(SubClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectPrivateDependency()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithPrivateDep::class);

        //then
        $this->assertInstanceOf(ClassWithPrivateDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->getMyClass());
    }

    /**
     * @test
     */
    public function shouldInjectSubClassDependency()
    {
        // given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->to(SubClassWithNoDep::class);
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithDep::class);

        //then
        $this->assertInstanceOf(ClassWithDep::class, $instance);
        $this->assertDependencyInjected(SubClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldCreateSubClass()
    {
        // given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->to(SubClassWithNoDep::class);
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertInstanceOf(SubClassWithNoDep::class, $instance);
    }

    /**
     * @test
     */
    public function shouldReturnBoundInstance()
    {
        // given
        $object = new ClassWithNoDep();

        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->toInstance($object);
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertSame($object, $instance);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenVarNotDefinedForInject()
    {
        //when
        CatchException::when($this->injector)->getInstance(ClassWithInvalidDep::class);

        //then
        CatchException::assertThat()->isInstanceOf(InjectorException::class);
    }

    /**
     * @test
     */
    public function shouldInjectNamedDependencyWhenNameWasNotBound()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithNamedDep::class);

        //then
        $this->assertInstanceOf(ClassWithNamedDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectNamedDependency()
    {
        // given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class, 'my_dep');
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithNamedDep::class);

        //then
        $this->assertInstanceOf(ClassWithNamedDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectProperNamedDependency()
    {
        // given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class, 'my_dep')->to(SubClassWithNoDep::class);
        $config->bind(ClassWithNoDep::class, 'other_dep');
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithNamedDep::class);

        //then
        $this->assertInstanceOf(ClassWithNamedDep::class, $instance);
        $this->assertDependencyInjected(SubClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldReturnInstanceByNameEvenWhenNameWasNotBound()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithNoDep::class, 'some_name');

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance);
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
        $instance = $injector->getInstance(ClassWithInjectorDep::class);

        //then
        $this->assertSame($injector, $instance->injector);
    }

    /**
     * @test
     */
    public function shouldModifyConfigAfterCreation()
    {
        //given
        $object = new ClassWithNoDep();

        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->toInstance($object);
        //ControllerTestCase exposes injectorConfig and allows users to add their bindings after injector is created

        //when
        $instance = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertSame($object, $instance);
    }

    /**
     * @test
     */
    public function shouldInjectDependencyByConstructor()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithConstructorDep::class);

        //then
        $this->assertInstanceOf(ClassWithConstructorDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldNotInjectDependencyByConstructorWhenConstructorHasParameterWithoutType()
    {
        //when
        CatchException::when($this->injector)->getInstance(ClassWithConstructorDepWithoutType::class);

        //then
        CatchException::assertThat()->isInstanceOf(InjectorException::class);
    }

    /**
     * @test
     */
    public function shouldInjectParentPrivateField()
    {
        //when
        $instance = $this->injector->getInstance(SubClassOfClassWithPrivateDep::class);

        //then
        $this->assertInstanceOf(SubClassOfClassWithPrivateDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->getMyClass());
    }

    /**
     * @test
     */
    public function shouldInjectThroughFactoryDependency()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ClassFactory::class);

        //when
        $instance = $injector->getInstance(ClassWithThroughDep::class);

        //then
        $this->assertInstanceOf(ClassWithThroughDep::class, $instance);
        $this->assertDependencyInjected(ClassCreatedByFactory::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectThroughFactoryDependencyWhenInterfaceIsNotImplemented()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ClassWithNoDep::class);

        //when
        CatchException::when($injector)->getInstance(ClassWithThroughDep::class);

        //then
        CatchException::assertThat()->isInstanceOf(InjectorException::class);
    }

    /**
     * @test
     */
    public function shouldInjectNamedThroughFactoryDependency()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class, 'through_dep')->throughFactory(ClassFactory::class);

        //when
        $instance = $injector->getInstance(ClassWithNamedThroughDep::class);

        //then
        $this->assertInstanceOf(ClassWithNamedThroughDep::class, $instance);
        $this->assertDependencyInjected(ClassCreatedByFactory::class, $instance->myClass);
    }

    /**
     * @test
     */
    public function shouldInjectThroughFactoryInSingletonScope()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ClassFactory::class)->in(Scope::SINGLETON);

        //when
        $instance1 = $injector->getInstance(ClassWithNoDep::class);
        $instance2 = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertSame($instance1, $instance2);
        $this->assertDependencyInjected(ClassCreatedByFactory::class, $instance1);
        $this->assertDependencyInjected(ClassCreatedByFactory::class, $instance2);
    }

    /**
     * @test
     */
    public function shouldInjectThroughFactoryInSingletonScopeAndStillBeAbleToCreateProperFactoryClass()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ClassFactory::class)->in(Scope::SINGLETON);
        $config->bind(ClassFactory::class)->in(Scope::SINGLETON);

        //when
        $instance1 = $injector->getInstance(ClassWithNoDep::class);
        $instance2 = $injector->getInstance(ClassFactory::class);

        //then
        $this->assertNotSame($instance1, $instance2);
        $this->assertDependencyInjected(ClassCreatedByFactory::class, $instance1);
        $this->assertDependencyInjected(ClassFactory::class, $instance2);
    }

    /**
     * @test
     */
    public function shouldInjectMultipleNamedDependenciesIntoConstructor()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class, 'my_dep')->to(SubClassWithNoDep::class);
        $config->bind(ClassWithPrivateDep::class, 'my_second_dep')->to(SubClassOfClassWithPrivateDep::class);

        //when
        $instance = $injector->getInstance(ClassWithNamedConstructorMultipleDep::class);

        //then
        $this->assertDependencyInjected(SubClassWithNoDep::class, $instance->myClass);
        $this->assertDependencyInjected(SubClassOfClassWithPrivateDep::class, $instance->secondClass);
    }

    /**
     * @test
     */
    public function shouldInjectOneNamedDependencyIntoDefinedConstructorParameter()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithPrivateDep::class, 'my_second_dep')->to(SubClassOfClassWithPrivateDep::class);

        //when
        $instance = $injector->getInstance(ClassWithNamedConstructorSingleNamedDep::class);

        //then
        $this->assertDependencyInjected(ClassWithPrivateDep::class, $instance->myClass);
        $this->assertDependencyInjected(SubClassOfClassWithPrivateDep::class, $instance->secondClass);
    }

    private function assertDependencyInjected($className, $instance)
    {
        $this->assertNotNull($instance, 'Dependency was not injected.');
        $this->assertInstanceOf($className, $instance);
    }
}
