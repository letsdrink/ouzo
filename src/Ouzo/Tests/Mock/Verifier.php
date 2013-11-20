<?php

namespace Ouzo\Tests\Mock;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Joiner;
use PHPUnit_Framework_ComparisonFailure;
use PHPUnit_Framework_ExpectationFailedException;

class Verifier
{
    private $mock;

    function __construct(SimpleMock $mock)
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
        $expected = MethodCall::newInstance($name, $arguments)->toString();
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