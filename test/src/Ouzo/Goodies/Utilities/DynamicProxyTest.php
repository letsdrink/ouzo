<?php
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

    public function fun($p1 = 1, $p2 = null, $p3 = 'a', $p4 = array('1' => 2), $p5 = self::C)
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

class TestMethodHandler
{
    public $calls = array();

    public function __call($name, $arguments)
    {
        $this->calls[] = array($name, $arguments);
        return "TestMethodHandler " . $name;
    }
}

interface TestInterface
{
    public function fun1(TestClass $p1);
}

class DynamicProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldInterceptMethodCalls()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ProxiedClass', $testMethodHandler);

        //when
        $proxy->fun1(1, 2);

        //then
        $this->assertEquals(array(array('fun1', array(1, 2))), $testMethodHandler->calls);
    }

    /**
     * @test
     */
    public function shouldReturnMethodHandlerResult()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ProxiedClass', $testMethodHandler);

        //when
        $result = $proxy->fun1(1, 2);

        //then
        $this->assertEquals("TestMethodHandler fun1", $result);
    }

    /**
     * @test
     */
    public function shouldBeInstanceOfGivenType()
    {
        //given
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ProxiedClass', null);

        //when
        $result = $proxy instanceof ProxiedClass;

        //then
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function shouldExtractMethodHandler()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ProxiedClass', $testMethodHandler);

        //when
        $result = DynamicProxy::extractMethodHandler($proxy);

        //then
        $this->assertEquals($testMethodHandler, $result);
    }

    /**
     * @test
     */
    public function shouldWorkWithDefaultParameters()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithMethodDefaultParameters', $testMethodHandler);

        //when
        $proxy->fun();

        //then
        $this->assertEquals(array(array('fun', array())), $testMethodHandler->calls);
    }

    /**
     * @test
     */
    public function shouldWorkWithTypedParameters()
    {
        //given
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithTypedParameters', $testMethodHandler);
        $param = new TestClass();

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals(array(array('fun1', array($param))), $testMethodHandler->calls);
    }

    /**
     * @test
     */
    public function shouldWorkForClassesWithConstructor()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithConstructor', $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    /**
     * @test
     */
    public function shouldWorkForClassesWithConstructorWithParameters()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithConstructorWithParams', $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    /**
     * @test
     */
    public function shouldWorkForClassWithStaticMethod()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithStaticMethod', $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    /**
     * @test
     */
    public function shouldWorkForClassWithAbstractMethod()
    {
        //given
        $testMethodHandler = new TestMethodHandler();

        //when
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithAbstractMethod', $testMethodHandler);

        //then
        $this->assertNotNull($proxy);
    }

    /**
     * @test
     */
    public function shouldCreateProxyForInterface()
    {
        //when
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\TestInterface', $testMethodHandler);
        $param = new TestClass();

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals(array(array('fun1', array($param))), $testMethodHandler->calls);
    }

    /**
     * @test
     */
    public function shouldCreateProxyForMethodWithParamsByRef()
    {
        //when
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithMethodThatTakesParamsByRef', $testMethodHandler);
        $param = array();

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals(array(array('fun1', array($param))), $testMethodHandler->calls);
    }
}
