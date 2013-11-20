<?php

namespace Ouzo\Tests\Mock;

class WhenBuilder
{
    private $mock;
    private $methodCall;

    function __construct(SimpleMock $mock)
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