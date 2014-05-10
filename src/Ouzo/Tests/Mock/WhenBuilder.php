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

    /**
     * @param mixed ...
     */
    function thenReturn()
    {
        foreach (func_get_args() as $result) {
            $this->mock->_stubbed_calls[] = new CallStub($this->methodCall, $result, null);
        }
    }

    /**
     * @param mixed ...
     */
    function thenThrow($exception)
    {
        foreach (func_get_args() as $exception) {
            $this->mock->_stubbed_calls[] = new CallStub($this->methodCall, null, $exception);
        }
    }
}