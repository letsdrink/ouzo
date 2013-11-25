<?php

namespace Ouzo\Tests\Mock;

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