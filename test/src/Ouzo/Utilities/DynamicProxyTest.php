<?php

namespace Ouzo\Utilities;

class ProxiedClass
{
    function fun1($a, $b)
    {
        return "result of fun1" . $a . $b;
    }

    function fun2($p1)
    {
    }
}

class ClassWithMethodDefaultParameters
{
    const C = 1;

    function fun($p1 = 1, $p2 = null, $p3 = 'a', $p4 = array('1' => 2), $p5 = self::C)
    {
    }
}

class TestClass {
}

class ClassWithTypedParameters
{
    function fun1(TestClass $p1)
    {
    }
    function fun2(array $p1)
    {
    }
}

class TestMethodHandler
{
    public $calls = array();

    function __call($name, $arguments)
    {
        $this->calls[] = array($name, $arguments);
        return "TestMethodHandler " . $name;
    }

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
        $testMethodHandler = new TestMethodHandler();
        $proxy = DynamicProxy::newInstance('Ouzo\Utilities\ClassWithTypedParameters', $testMethodHandler);
        $param = new TestClass();

        //when
        $proxy->fun1($param);

        //then
        $this->assertEquals(array(array('fun1', array($param))), $testMethodHandler->calls);
    }
}
 