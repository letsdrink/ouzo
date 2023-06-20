<?php
/*
 * Copyright (c) Ouzo contributors, https://github.com/letsdrink/ouzo
 * This file is made available under the MIT License (view the LICENSE file for more information).
 */

namespace Ouzo\Utilities;

class ProxiedClass
{
    public function fun1($a, $b)
    {
        return "result of fun1" . $a . $b;
    }

    public function fun2($p1)
    {
    }
}

class ClassWithMethodDefaultParameters
{
    const C = 1;

    public function fun($p1 = 1, $p2 = null, $p3 = 'a', $p4 = ['1' => 2], $p5 = self::C)
    {
    }
}

class TestClass
{
}

class ClassWithTypedParameters
{
    public function fun1(TestClass $p1)
    {
    }

    public function fun2(array $p1)
    {
    }
}

class ClassWithConstructor
{
    public function __construct()
    {
    }
}

class ClassWithConstructorWithParams
{
    public function __construct(array $a)
    {
    }
}

class ClassWithStaticMethod
{
    public function fun1(TestClass $p1)
    {
    }

    public static function fun2()
    {
    }
}

abstract class ClassWithAbstractMethod
{
    public function fun1(TestClass $p1)
    {
    }

    abstract public function fun2();
}

class ClassWithMethodThatTakesParamsByRef
{
    public function fun1(array &$p1)
    {
    }
}

class ClassWithMethodThatTakesVararg
{
    public function fun1(...$vararg)
    {
    }
}

class TestMethodHandler
{
    public $calls = [];

    public function __call($name, $arguments)
    {
        $this->calls[] = [$name, $arguments];
        return "TestMethodHandler " . $name;
    }
}

class NullReturningTestMethodHandler
{
    public function __call($name, $arguments)
    {
        return null;
    }
}

interface TestInterface
{
    public function fun1(TestClass $p1);
}

interface StaticTestInterface
{
    public static function fun1(TestClass $p1);
}

if (version_compare('7.1.0', PHP_VERSION, '<=')) {
    include __DIR__ . '/DynamicProxyClassesFor71.php';
}

use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DynamicProxyTest extends TestCase
{
    #[Test]
    public function shouldInterceptMethodCalls()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ProxiedClass::class, $testMethodHandler);

        //when
        $proxy->fun1(1, 2);

        //then
        $this->assertEquals([['fun1', [1, 2]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldReturnMethodHandlerResult()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ProxiedClass::class, $testMethodHandler);

        //when
        $result = $proxy->fun1(1, 2);

        //then
        $this->assertEquals("TestMethodHandler fun1", $result);
    }

    #[Test]
    public function shouldBeInstanceOfGivenType()
    {
        //given
        $proxy = DynamicProxy::newInstance(ProxiedClass::class, null);

        //when
        $result = $proxy instanceof ProxiedClass;

        //then
        $this->assertTrue($result);
    }

    #[Test]
    public function shouldExtractMethodHandler()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ProxiedClass::class, $testMethodHandler);

        //when
        $result = DynamicProxy::extractMethodHandler($proxy);

        //then
        $this->assertEquals($testMethodHandler, $result);
    }

    #[Test]
    public function shouldWorkWithDefaultParameters()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMethodDefaultParameters::class, $testMethodHandler);

        //when
        $proxy->fun();

        //then
        $this->assertEquals([['fun', []]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldWorkWithTypedParameters()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithTypedParameters::class, $testMethodHandler);
        $param = new TestClass();

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals([['fun1', [$param]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldWorkForClassesWithConstructor()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance(ClassWithConstructor::class, $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    #[Test]
    public function shouldWorkForClassesWithConstructorWithParameters()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance(ClassWithConstructorWithParams::class, $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    #[Test]
    public function shouldWorkForClassWithStaticMethod()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance(ClassWithStaticMethod::class, $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    #[Test]
    public function shouldWorkForAbstractStaticMethod()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance(StaticTestInterface::class, $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    #[Test]
    public function shouldWorkForClassWithAbstractMethod()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance(ClassWithAbstractMethod::class, $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    #[Test]
    public function shouldCreateProxyForInterface()
    {
        //when
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(TestInterface::class, $testMethodHandler);
        $param = new TestClass();

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals([['fun1', [$param]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldCreateProxyForMethodWithParamsByRef()
    {
        //when
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMethodThatTakesParamsByRef::class, $testMethodHandler);
        $param = [];

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals([['fun1', [$param]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldCreateProxyForMethodWithVararg()
    {
        //when
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMethodThatTakesVararg::class, $testMethodHandler);
        $param = [];

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals([['fun1', [$param]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldCreateProxyForMethodWithPrimitiveTypes()
    {
        if (version_compare('7.1.0', PHP_VERSION, '>')) {
            $this->markTestSkipped("Test only for PHP 7.1+");
        }
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMethodThatTakesPrimitives::class, $testMethodHandler);

        //when
        $proxy->fun1(1, [], new TestClass());

        //then
        $this->assertEquals([['fun1', [1, [], new TestClass()]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldCreateProxyForMethodWithReturnType()
    {
        if (version_compare('7.1.0', PHP_VERSION, '>')) {
            $this->markTestSkipped("Test only for PHP 7.1+");
        }
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMethodThatReturnType::class, $testMethodHandler);

        //when
        $proxy->fun1(1);

        //then
        $this->assertEquals([['fun1', [1]]], $testMethodHandler->calls);
    }

    #[Test]
    public function shouldNotCastNull()
    {
        //given
        $testMethodHandler = new NullReturningTestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithNullReturningMethod::class, $testMethodHandler);

        //when
        $result = $proxy->fun1(1);

        //then
        $this->assertNull($result);
    }

    #[Test]
    public function shouldNotCastMixed()
    {
        //given
        $testMethodHandler = new NullReturningTestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMixedReturningMethod::class, $testMethodHandler);

        //when
        $result = $proxy->fun1(1);

        //then
        $this->assertNull($result);
    }

    #[Test]
    public function shouldImplementsMockInterface()
    {
        //given
        $testMethodHandler = new NullReturningTestMethodHandler();
        $proxy = DynamicProxy::newInstance(ClassWithMixedReturningMethod::class, $testMethodHandler);

        //when
        $result = $proxy instanceof MockInterface;

        //then
        $this->assertTrue($result);
    }

}
