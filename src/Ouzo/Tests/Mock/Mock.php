<?php

namespace Ouzo\Tests\Mock;

use Ouzo\Utilities\DynamicProxy;

class SimpleMock
{
    public $_stubbed_calls = array();
    public $_called_methods = array();

    function __call($name, $arguments)
    {
        $methodCall = new MethodCall($name, $arguments);
        $this->_called_methods[] = $methodCall;

        foreach ($this->_stubbed_calls as $stubbed_call) {
            if ($stubbed_call->matches($methodCall)) {
                return $stubbed_call->evaluate();
            }
        }
        return null;
    }
}

class Mock
{
    public static function mock($className = null)
    {
        $mock = new SimpleMock();
        if (!$className) {
            return $mock;
        }
        return DynamicProxy::newInstance($className, $mock);;
    }

    public static function when($mock)
    {
        return new WhenBuilder(self::_extractMock($mock));
    }

    public static function verify($mock)
    {
        return new Verifier(self::_extractMock($mock));
    }

    private static function _extractMock($mock)
    {
        if ($mock instanceof SimpleMock) {
            return $mock;
        }
        return DynamicProxy::extractMethodHandler($mock);
    }

    public static function any()
    {
        return new AnyArgument();
    }

    public static function anyArgList()
    {
        return new AnyArgumentList();
    }
} 