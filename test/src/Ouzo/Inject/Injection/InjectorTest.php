<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

use Application\MyNamespace\ClassWithNamespace;
use Ouzo\Injection\Creator\ProxyManagerInstanceCreator;
use Ouzo\Injection\Injector;
use Ouzo\Injection\InjectorConfig;
use Ouzo\Injection\InjectorException;
use Ouzo\Injection\Scope;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\TestCase;
use ProxyManager\Configuration;
use ProxyManager\Proxy\VirtualProxyInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class InjectorTest extends TestCase
{
    private Injector $injector;

    public function setUp(): void
    {
        parent::setUp();
        $this->injector = new Injector();
    }

    #[Test]
    public function shouldCreateInstanceByName()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldCreateEagerSingleton()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(ClassWithNoDep::class)->asEagerSingleton();
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithNoDep::class);

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance);
    }

    #[Test]
    public function shouldInjectDependency()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithDep::class);

        //then
        $this->assertInstanceOf(ClassWithDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    #[Test]
    public function shouldInjectDeepDependency()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithDeepDep::class);

        //then
        $this->assertInstanceOf(ClassWithDeepDep::class, $instance);
        $this->assertDependencyInjected(ClassWithDep::class, $instance->classWithDep);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->classWithDep->myClass);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldInjectPrivateDependency()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithPrivateDep::class);

        //then
        $this->assertInstanceOf(ClassWithPrivateDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->getMyClass());
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldThrowExceptionWhenVarNotDefinedForInject()
    {
        //when
        CatchException::when($this->injector)->getInstance(ClassWithInvalidDep::class);

        //then
        CatchException::assertThat()->isInstanceOf(InjectorException::class);
    }

    #[Test]
    public function shouldInjectNamedDependencyWhenNameWasNotBound()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithNamedDep::class);

        //then
        $this->assertInstanceOf(ClassWithNamedDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldReturnInstanceByNameEvenWhenNameWasNotBound()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithNoDep::class, 'some_name');

        //then
        $this->assertInstanceOf(ClassWithNoDep::class, $instance);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldInjectDependencyByConstructor()
    {
        //when
        $instance = $this->injector->getInstance(ClassWithConstructorDep::class);

        //then
        $this->assertInstanceOf(ClassWithConstructorDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
    }

    #[Test]
    public function shouldNotInjectDependencyByConstructorWhenConstructorHasParameterWithoutType()
    {
        //when
        CatchException::when($this->injector)->getInstance(ClassWithConstructorDepWithoutType::class);

        //then
        CatchException::assertThat()->isInstanceOf(InjectorException::class);
    }

    #[Test]
    public function shouldInjectParentPrivateField()
    {
        //when
        $instance = $this->injector->getInstance(SubClassOfClassWithPrivateDep::class);

        //then
        $this->assertInstanceOf(SubClassOfClassWithPrivateDep::class, $instance);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->getMyClass());
    }

    #[Test]
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
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
        $this->assertTrue($instance->myClass->isThroughFactoryFlag());
    }

    #[Test]
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

    #[Test]
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
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->myClass);
        $this->assertTrue($instance->myClass->isThroughFactoryFlag());
    }

    #[Test]
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
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance1);
        $this->assertTrue($instance1->isThroughFactoryFlag());
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance2);
        $this->assertTrue($instance2->isThroughFactoryFlag());
    }

    #[Test]
    public function shouldInjectThroughFactoryInSingletonScopeAndStillBeAbleToCreateProperFactoryClass()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ClassFactory::class)->in(Scope::SINGLETON);
        $config->bind(ClassFactory::class)->in(Scope::SINGLETON);

        //when
        /** @var ClassWithNoDep $instance1 */
        $instance1 = $injector->getInstance(ClassWithNoDep::class);
        $instance2 = $injector->getInstance(ClassFactory::class);

        //then
        $this->assertNotSame($instance1, $instance2);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance1);
        $this->assertTrue($instance1->isThroughFactoryFlag());
        $this->assertDependencyInjected(ClassFactory::class, $instance2);
    }

    #[Test]
    public function injectThroughFactoryShouldBeCreatedEagerly()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ExceptionThrowingClassFactory::class);

        //when
        CatchException::when($injector)->getInstance(ClassWithThroughDep::class);

        //then
        CatchException::assertThat()->hasMessage('Should never be invoked! It means lazy is not working.');
    }

    #[Test]
    public function injectThroughFactoryShouldBeCreatedAsLazy()
    {
        //given
        $config = new InjectorConfig();
        $config->setLazyInstanceCreator(new ProxyManagerInstanceCreator(new Configuration()));
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ExceptionThrowingClassFactory::class);

        //when
        $instance = $injector->getInstance(ClassWithThroughDep::class);

        //then
        $this->assertInstanceOf(ClassWithThroughDep::class, $instance);
        $this->assertInstanceOf(VirtualProxyInterface::class, $instance->myClass);
    }

    #[Test]
    public function injectThroughFactoryShouldBeCreatedAsLazyInSingletonScope()
    {
        //given
        $config = new InjectorConfig();
        $config->setLazyInstanceCreator(new ProxyManagerInstanceCreator(new Configuration()));
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ExceptionThrowingClassFactory::class)->in(Scope::SINGLETON);

        //when
        $instance1 = $injector->getInstance(ClassWithThroughDep::class);
        $instance2 = $injector->getInstance(ClassWithThroughDep::class);

        //then
        $this->assertSame($instance1->myClass, $instance2->myClass);
    }

    #[Test]
    public function injectThroughFactoryShouldBeCreatedAsLazyInPrototypeScope()
    {
        //given
        $config = new InjectorConfig();
        $config->setLazyInstanceCreator(new ProxyManagerInstanceCreator(new Configuration()));
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ExceptionThrowingClassFactory::class);

        //when
        $instance1 = $injector->getInstance(ClassWithThroughDep::class);
        $instance2 = $injector->getInstance(ClassWithThroughDep::class);

        //then
        $this->assertNotSame($instance1->myClass, $instance2->myClass);
    }

    #[Test]
    public function injectThroughFactoryShouldBeCreatedAsLazyAndCreatedWhenMethodOnProxyIsInvoked()
    {
        //given
        $config = new InjectorConfig();
        $config->setLazyInstanceCreator(new ProxyManagerInstanceCreator(new Configuration()));
        $injector = new Injector($config);
        $config->bind(ClassWithNoDep::class)->throughFactory(ExceptionThrowingClassFactory::class);

        $instance = $injector->getInstance(ClassWithThroughDep::class);

        //when
        CatchException::when($instance->myClass)->someMethod();

        //then
        CatchException::assertThat()->hasMessage('Should never be invoked! It means lazy is not working.');
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function shouldInjectFieldDependencyWithoutFQN()
    {
        //given
        $config = new InjectorConfig();
        $injector = new Injector($config);

        //when
        $instance = $injector->getInstance(ClassWithTypedProperty::class);

        //then
        $this->assertDependencyInjected(ClassWithNamespace::class, $instance->myClass);
        $this->assertDependencyInjected(ClassWithNoDep::class, $instance->mySecondClass);
    }

    #[Test]
    public function shouldInjectListOfConstructorDependencies()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(SampleInterface::class)->to(SampleImpl1::class, SampleImpl2::class);

        $injector = new Injector($config);

        //when
        /** @var ClassWithListOfConstructorDep $instance */
        $instance = $injector->getInstance(ClassWithListOfConstructorDep::class);

        //then
        $sampleInterfaces = Arrays::map($instance->getSampleInterfaces(), fn($object) => $object::class);
        Assert::thatArray($sampleInterfaces)->containsOnly(SampleImpl1::class, SampleImpl2::class);
    }

    #[Test]
    public function shouldInjectNamedListOfConstructorDependencies()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(SampleInterface::class, 'SampleList')->to(SampleImpl1::class, SampleImpl2::class);
        $config->bind(SampleInterface::class, 'OtherList')->to(SampleImpl2::class);
        $config->bind(SampleInterface::class)->to(SampleImpl1::class);

        $injector = new Injector($config);

        //when
        /** @var ClassWithNamedListOfConstructorDep $instance */
        $instance = $injector->getInstance(ClassWithNamedListOfConstructorDep::class);

        //then
        $sampleInterfaces = Arrays::map($instance->getSampleInterfaces(), fn($object) => $object::class);
        Assert::thatArray($sampleInterfaces)->containsOnly(SampleImpl1::class, SampleImpl2::class);
    }

    #[Test]
    public function shouldInjectListOfDependencies()
    {
        //given
        $config = new InjectorConfig();
        $config->bind(SampleInterface::class)->to(SampleImpl1::class, SampleImpl2::class);

        $injector = new Injector($config);

        //when
        /** @var ClassWithListOfDep $instance */
        $instance = $injector->getInstance(ClassWithListOfDep::class);

        //then
        $sampleInterfaces = Arrays::map($instance->getSampleInterfaces(), fn($object) => $object::class);
        Assert::thatArray($sampleInterfaces)->containsOnly(SampleImpl1::class, SampleImpl2::class);
    }

    private function assertDependencyInjected($className, $instance)
    {
        $this->assertNotNull($instance, 'Dependency was not injected.');
        $this->assertInstanceOf($className, $instance);
    }
}
