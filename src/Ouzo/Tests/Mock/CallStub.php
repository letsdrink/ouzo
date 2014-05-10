<?php
namespace Ouzo\Tests\Mock;

class CallStub
{
    public $methodCall;
    public $result;
    public $exception;

    public function __construct($methodCall, $result, $exception)
    {
        $this->methodCall = $methodCall;
        $this->result = $result;
        $this->exception = $exception;
    }

    public function evaluate()
    {
        if ($this->exception) {
            throw $this->exception;
        }
        return $this->result;
    }

    public function matches(MethodCall $methodCall)
    {
        $matcher = new MethodCallMatcher($this->methodCall->name, $this->methodCall->arguments);
        return $matcher->matches($methodCall);
    }
}