<?php

namespace Ouzo\Tests\Mock;

use Ouzo\Utilities\Joiner;

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

    public static function newInstance($name, $arguments)
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

    public static function matches($name, $arguments)
    {
        return function (MethodCall $methodCall) use($name, $arguments) {
            return $methodCall->name == $name && $methodCall->arguments === $arguments;
        };
    }
}