<?php

namespace Ouzo\Tests\Mock;

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