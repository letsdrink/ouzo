<?php

namespace Ouzo\Tests;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\DynamicProxy;
use Ouzo\Utilities\Joiner;
use PHPUnit_Framework_ComparisonFailure;
use PHPUnit_Framework_ExpectationFailedException;

class CallStub
{
    public $methodCall;
    public $result;

    function __construct($methodCall, $result)
    {
        $this->methodCall = $methodCall;
        $this->result = $result;
    }
}

class MethodCall
{
    public $name;
    public $arguments;

    function __construct($name, $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function toString()
    {
        return $this->name . '(' . Joiner::on(', ')->join($this->arguments) . ')';
    }

    public static function create($name, $arguments)
    {
        return new MethodCall($name, $arguments);
    }

    public static function hasName($name)
    {
        return function ($callStub) use ($name) {
            return $callStub->name == $name;
        };
    }

    public static function toStringFunction()
    {
        return function (MethodCall $methodCall) {
            return $methodCall->toString();
        };
    }
}

class WhenBuilder
{
    private $mock;
    private $methodCall;

    function __construct(Mock $mock)
    {
        $this->mock = $mock;
    }

    function __call($name, $arguments)
    {
        $this->methodCall = new MethodCall($name, $arguments);
        return $this;
    }

    function thenReturn($result)
    {
        $this->mock->_stubbed_calls[] = new CallStub($this->methodCall, $result);
    }
}

class Verifier
{
    private $mock;

    function __construct($mock)
    {
        $this->mock = $mock;
    }

    function __call($name, $arguments)
    {
        foreach ($this->mock->_called_methods as $called_method) {
            if ($called_method->name == $name && $called_method->arguments === $arguments) {
                return $this;
            }
        }
        $calls = $this->actualCalls();
        $expected = MethodCall::create($name, $arguments)->toString();
        $this->fail("Expected method was not called", $expected, $calls);
    }

    private function fail($description, $expected, $actual)
    {
        throw new PHPUnit_Framework_ExpectationFailedException(
            $description,
            new PHPUnit_Framework_ComparisonFailure($expected, $actual, $expected, $actual)
        );
    }

    private function actualCalls()
    {
        if (empty($this->mock->_called_methods)) {
            return "no interactions";
        }
        return Joiner::on(', ')->join(Arrays::map($this->mock->_called_methods, MethodCall::toStringFunction()));
    }

}

class Mock
{
    public $_stubbed_calls = array();
    public $_called_methods = array();
    public $_className;

    function __construct($className)
    {
        $this->_className = $className;
    }

    public static function mock($className)
    {
        $mock = new Mock($className);
        return DynamicProxy::newInstance($className, $mock);
    }

    public static function when($mock)
    {
        return new WhenBuilder(DynamicProxy::extractMethodHandler($mock));
    }

    public static function verify($mock)
    {
        return new Verifier(DynamicProxy::extractMethodHandler($mock));
    }

    function __call($name, $arguments)
    {
        $methodCall = new MethodCall($name, $arguments);
        $this->_called_methods[] = $methodCall;

        foreach ($this->_stubbed_calls as $stubbed_call) {
            if ($stubbed_call->methodCall == $methodCall) {
                return $stubbed_call->result;
            }
        }
        return null;
    }
} 