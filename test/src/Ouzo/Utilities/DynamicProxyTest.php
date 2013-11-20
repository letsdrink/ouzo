<?php

namespace Ouzo\Utilities;


class ProxiedClass
{
    function fun1($a, $b)
    {
        return "result of fun1" . $a . $b;
    }

    function fun2()
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
        $proxy = DynamicProxy::create('Ouzo\Utilities\ProxiedClass', $testMethodHandler);

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
        $proxy = DynamicProxy::create('Ouzo\Utilities\ProxiedClass', $testMethodHandler);

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
        $proxy = DynamicProxy::create('Ouzo\Utilities\ProxiedClass', null);

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
        $proxy = DynamicProxy::create('Ouzo\Utilities\ProxiedClass', $testMethodHandler);

        //when
        $result = DynamicProxy::extractMethodHandler($proxy);

        //then
        $this->assertEquals($testMethodHandler, $result);
    }
}
 